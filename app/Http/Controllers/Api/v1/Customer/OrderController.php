<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Helpers\BusinessHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictOrderResource;
use App\Models\BusinessSetting;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\CustomerWallet;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\Shop;
use DateTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class OrderController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::withAll()->where('customer_id', '=', $this->getUserId())->orderByDesc('updated_at')->get();
        return StrictOrderResource::collection($orders);
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|StrictOrderResource|Application|ResponseFactory
    {
        $order_validated_data = $this->validate($request, Order::verifyRules(), Order::verifyMessages());
        $customer_id = $order_validated_data['customer_id'];

        $order = new Order();

        $order_payment_validated_data = $this->validate($request, OrderPayment::verifyRules());
        $shop_id = $order_validated_data['shop_id'];
        $shop = Shop::with('module')->findOrFail($shop_id);
        $order_type = $order_validated_data['order_type'];

        if (!$shop->open_for_delivery && $order_type == Order::$delivery_type) {
            return $this->errorResponse('Currently shop is not accepting delivery orders');
        } elseif (!$shop->take_away && $order_type == Order::$self_pickup_type) {
            return $this->errorResponse('Currently shop is not accepting take away orders');
        }


        $carts = Cart::with(['product', 'productOption', 'addons', 'addons.addon'])
            ->where('shop_id', $order_validated_data['shop_id'])
            ->where('customer_id', $this->getUserId())
            ->get();

        if (count($carts) == 0) {
            return $this->validationErrorResponse(['carts' => 'Carts is empty']);
        }


        $order_amount = 0;
        $coupon_discount = 0;
        $delivery_charge = 0;
        $payment_charge = 0;

        foreach ($carts as $cart) {
            //Note: ----- Availability Check Logic
            if (isset($cart->product->available_from) && isset($cart->product->available_to)) {
                $from = DateTime::createFromFormat('H:i:s', $cart->product->available_from);
                $to = DateTime::createFromFormat('H:i:s', $cart->product->available_to);
                $now = DateTime::createFromFormat('H:i:s', date('H:i:s', time()));
                if ($now < $from || $now > $to) {
                    return $this->errorResponse('Some product is not available at the moment');
                }
            }

            $order_amount += $cart->getCartTotal();
        }

        if ($order_amount < $shop->min_order_amount) {
            return $this->validationErrorResponse(['order' => 'Minimum order amount is not fulfilled']);
        }


        if ($order_type == Order::$delivery_type) {
            if (isset($order_validated_data['customer_address_id'])) {
                //TODO::: Change to Original
                $customerAddress = CustomerAddress::findOrFail($order_validated_data['customer_address_id']);
                if (!$customerAddress->isInZone($shop->zone_id)) {
                    return $this->errorResponse('Your location is not in shop\'s zone. Change a delivery address');
                }
                $delivery_charge = $shop->getDeliveryFee($customerAddress);
                if ($delivery_charge == null) {
                    return $this->validationErrorResponse(['delivery' => 'Calculation in delivery fees in wrong']);
                } elseif ($delivery_charge == -1) {
                    return $this->validationErrorResponse(['delivery' => 'Delivery location is too far from shop']);
                }
            } else {
                return $this->errorResponse('Please provide delivery address');
            }
        } elseif ($order_type == Order::$self_pickup_type) {
            //IF any
        } else {
            return $this->errorResponse('This order type is not available at moment');
        }

        if (isset($order_validated_data['coupon_id'])) {
            $coupon = Coupon::active()->with([
                'orders' => function ($q) use ($customer_id, $shop_id) {
                    $q->where('customer_id', $customer_id);
                }
            ])->where(function ($query) use ($shop_id) {
                $query->where('shop_id', $shop_id)->orWhere('shop_id', null);
            })->where(function ($query) use ($shop) {
                $query->where('module_id', $shop->module_id)->orWhere('module_id', null);
            });

            if ($shop->zone_id != null) {
                $coupon->where(function ($query) use ($shop) {
                    $query->where('zone_id', $shop->zone_id)->orWhere('zone_id', null);
                });
            }
            $coupon = $coupon->findOrFail($order_validated_data['coupon_id']);

            if (!$coupon->verify()) {
                return $this->validationErrorResponse(['coupon_id' => 'This coupon is already used']);
            }

            $coupon_discount = $coupon->getDiscountFromOrder($order_amount, $delivery_charge);
        }

        $tax = $shop->getTaxFromOrder($order_amount);
        $packaging_charge = $shop->packaging_charge;
        $order_commission = $shop->getAdminCommissionFromOrder($order_amount + $tax + $packaging_charge);
        $delivery_commission = BusinessHelper::getDeliveryCommissionFromOrder($delivery_charge);


        $total_without_payment_charge = $order_amount + $tax + $packaging_charge + $delivery_charge + $order_commission + $delivery_commission - $coupon_discount;

        if ($order_payment_validated_data['payment_type'] == 'wallet') {
            $payment_charge = BusinessHelper::getPaymentChargeFromOrder($total_without_payment_charge);
        }


        $total = $total_without_payment_charge + $payment_charge;


        $total_from_request = $order_validated_data['total'];
        $total = round($total, 2);

        if (round(abs($total - $total_from_request), 2) > 0.01) {
            return $this->validationErrorResponse(['total' => 'This total is not match with original order']);
        }


        $order->notes = $order_validated_data['notes'] ?? null;
        $order->order_type = $order_validated_data['order_type'];
        $order->order_amount = $order_amount;
        $order->packaging_charge = $packaging_charge;
        $order->tax = $tax;
        $order->module_id = $shop->module->id;
        $order->order_commission = $order_commission;
        $order->delivery_charge = $delivery_charge;
        $order->delivery_commission = $delivery_commission;
        $order->payment_charge = $payment_charge;
        $order->coupon_discount = $coupon_discount;
        $order->total = $total;

        $order->customer_id = $customer_id;
        $order->shop_id = $shop_id;
        if ($order->order_type == Order::$delivery_type) {
            $order->customer_address_id = $order_validated_data['customer_address_id'];
        }
        $order->coupon_id = $order_validated_data['coupon_id'] ?? null;


        DB::transaction(function () use ($request, $total, $carts, $order_payment_validated_data, $order) {
            $order->save();
            $order->saveImages($request);

            $order_payment_validated_data = [
                ...$order_payment_validated_data,
                'order_id' => $order->id,
                'total_payment' => $order->total
            ];
            $order_payment = new OrderPayment($order_payment_validated_data);
            $order_payment->save();

            $order_status = new OrderStatus(['order_id' => $order->id, 'status' => OrderStatus::$place_order_status]);
            $order_status->save();

            foreach ($carts as $cart) {
                $order_item = new OrderItem();
                $order_item->product_id = $cart->product_id;
                $order_item->product_option_id = $cart->product_option_id;
                $order_item->order_id = $order->id;
                $order_item->price = $cart->productOption->price;
                $order_item->calculated_price = $cart->productOption->calculated_price;
                $order_item->discount = $cart->productOption->discount;
                $order_item->discount_type = $cart->productOption->discount_type;
                $order_item->quantity = $cart->quantity;
                $order_item->save();

                if ($cart->addons != null) {
                    foreach ($cart->addons as $c_addon) {
                        $order_item_addon = new OrderItemAddon();
                        $order_item_addon->order_item_id = $order_item->id;
                        $order_item_addon->addon_id = $c_addon->addon_id;
                        $order_item_addon->price = $c_addon->addon->price;
                        $order_item_addon->quantity = $c_addon->quantity;
                        $order_item_addon->save();
                    }
                    foreach ($cart->addons as $addon) {
                        $addon->delete();
                    }
                }
                $cart->delete();
            }
        });

        $order->loadAll();
        $order->sendOrderNotificationToSeller(
            BusinessSetting::_get(BusinessSetting::$new_order_for_seller_notification)
        );
        if (BusinessSetting::isEnableAdminOrderNotification()) {
            $order->sendOrderNotificationToAdmin(
                BusinessSetting::_get(BusinessSetting::$new_order_for_seller_notification)
            );
        }

        return new StrictOrderResource($order);
    }


    public function show(Request $request, $id): StrictOrderResource
    {
        $order = Order::withAll()->where('customer_id', '=', $this->getUserId())->findOrFail($id);
        return new StrictOrderResource($order);
    }


    public function invoice(Request $request, $id): StrictOrderResource
    {
        $invoice_otp = $request->query->get('i_otp');
        $order = Order::withAll()->where('invoice_otp', '=', $invoice_otp)->findOrFail($id);
        return new StrictOrderResource($order);
    }

    public function update(Request $request, $id): StrictOrderResource
    {
        $order = Order::findOrFail($id);
        $status = $request->get('status');
        if (isset($status)) {
            $description = $request->get('description');
            if ($status == OrderStatus::$resubmit_status) {
                $order->resubmit($description);
            }
            $order->saveImages($request);
        }

        $order->loadAll();
        return new StrictOrderResource($order);
    }

    /// ========================= Order Actions =======================================///
    ///
    public function cancel(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::where('customer_id', $this->getUserId())->findOrFail($id);

        if ($order->canCancel()) {
            $order->cancelByCustomer($request->get('description'));
            $order->loadAll();
            return new StrictOrderResource($order);
        } else {
            return $this->errorResponse('You can\'t cancel order');
        }
    }

    public function resubmit(Request $request, $id): StrictOrderResource
    {
        $order = Order::where('customer_id', $this->getUserId())->findOrFail($id);
        $order->resubmit($request->get('description'));
        $order->saveImages($request);
        $order->loadAll();
        return new StrictOrderResource($order);
    }

    public function pickup(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::where('customer_id', $this->getUserId())->findOrFail($id);
        if ($order->canPickup()) {
            $order->pickupByCustomer($request->get('description'));
            $order->loadAll();
            return new StrictOrderResource($order);
        } else {
            return $this->errorResponse('You can\'t pickup order');
        }
    }

    public function pay(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::where('customer_id', $this->getUserId())->findOrFail($id);
        if (!$order->canPay()) {
            return $this->errorResponse('You can\'t pay this order');
        }
        $order_payment = OrderPayment::where('order_id', $id)->first();
        $wallet = CustomerWallet::where('customer_id', $this->getUserId())->first();
        if ($wallet->balance < $order_payment->total_payment) {
            return $this->validationErrorResponse(['wallet' => 'You have not enough money in wallet']);
        }
        $order->payByCustomerWallet();
        $order->loadAll();
        return new StrictOrderResource($order);
    }


}

