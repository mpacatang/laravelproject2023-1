<?php

namespace App\Models;

use App\Helpers\Util\StringUtil;
use App\Jobs\ProcessAutoAssignDeliveryBoy;
use App\Jobs\ProcessOrderInfoMail;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


/**
 * @property mixed $id
 * @property string $image
 * @property mixed $total
 * @property mixed $customer_id
 * @property mixed $shop_id
 * @property int|mixed $tax
 * @property int|mixed $coupon_discount
 * @property int|mixed $delivery_charge
 * @property float|int|mixed $order_amount
 * @property mixed|string $order_type
 * @property mixed $complete
 * @property mixed $delivery_boy_id
 * @property mixed $otp
 * @property float|mixed $payment_charge
 * @property float|mixed $delivery_commission
 * @property float|mixed $order_commission
 * @property Carbon|mixed $ready_at
 * @property OrderItem[] $items
 * @property mixed $assign_delivery_boy_id
 * @property mixed $longitude
 * @property mixed $latitude
 * @property mixed $customer
 * @property mixed $order
 * @property mixed $paid_amount
 * @property mixed $change_amount
 * @property mixed $packaging_charge
 * @property int|mixed $delivery_boy_revenue_amount
 * @property float|int|mixed $shop_revenue_amount
 * @property float|int|mixed $admin_revenue_amount
 * @property mixed $notes
 * @property mixed|null $coupon_id
 * @property mixed $customer_address_id
 * @property HigherOrderBuilderProxy|mixed $module_id
 * @property mixed $invoice_otp
 * @property Shop $shop
 * @property mixed $assign_delivery_boy
 * @property Module $module
 */
class Order extends BaseModel
{

    // --------------------------- Defaults ---------------------------------------//
    use HasFactory;


    public static string $delivery_type = 'delivery';
    public static string $pos = 'pos';
    public static string $self_pickup_type = 'self_pickup';

    protected $guarded = [];

    protected $casts = ['complete' => 'boolean', 'ready_at' => 'datetime'];


    public static function withAll(): Builder|self
    {
        return Order::with(
            [
                'payments',
                'items',
                'items.product',
                'items.product.productImages',
                'items.productOption',
                'items.addons',
                'items.addons.addon',
                'address',
                'customer',
                'statuses',
                'medias',
                'shop',
                'coupon',
                'deliveryBoy',
                'assignDeliveryBoy'
            ]
        );
    }

    public function loadAll(): Order
    {
        return $this->load(
            [
                'payments',
                'items',
                'items.product',
                'items.product.productImages',
                'items.productOption',
                'items.addons',
                'items.addons.addon',
                'address',
                'customer',
                'statuses',
                'medias',
                'shop',
                'coupon',
                'deliveryBoy',
                'assignDeliveryBoy'
            ]
        );
    }

    // --------------------------- Rules ---------------------------------------//


    public static function verifyRules($id = null,): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'customer_id' => ['required'],
            'shop_id' => ['required', Rule::exists('shops', 'id')],
            'order_type' => ['required', 'in:delivery,self_pickup'],
            'customer_address_id' => ['required_if:order_type,"delivery"', Rule::exists('customer_addresses', 'id')],
            'coupon_id' => [Rule::exists('coupons', 'id')],
            'coupon_discount' => ['required_with:coupon_id', 'numeric', 'min:0'],
            'order_amount' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'delivery_charge' => ['required_if:order_type,"delivery"', 'numeric', 'min:0'],
            'notes' => [],
        ];
    }


    public static function verifyMessages(): array
    {
        return [
            'customer_address_id.required_if' => 'Please choose address, if you need order delivery',
            'coupon_discount.required_with' => 'Please add discount, if you choose coupon',
        ];
    }

    public static function deliveryRules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return ['otp' => ['required', 'numeric',]];
    }

    public static function deliveryMessages(): array
    {
        return ['otp.required' => 'Please provide an otp to deliver order',];
    }


    // ------------------------- Functions ---------------------------------------------------//

    public function saveImages(Request $request, $key = 'medias'): bool
    {
        if ($request->get($key)) {
            $imageList = $request->get($key);
            if (is_array($imageList)) {
                foreach ($imageList as $image) {
                    OrderMedia::saveOrderMedia($this, $image);
                }
            } else {
                OrderMedia::saveOrderMedia($this, $imageList);
            }
        }

        return false;
    }

    // ------------------------- Order Flow Rule ---------------------------------------------------//

    public function canPay(): bool
    {
        $payment = $this->payments()->latest()->first();
        $order_status = $this->statuses()->latest()->first();
        if ($payment->payment_status == OrderPayment::$paid_status) {
            return false;
        }
        if ($order_status->isCancelled()) {
            return false;
        }
        return true;
    }

    public function canCancel(): bool
    {
        $order_status = OrderStatus::where('order_id', $this->id)->latest()->first();
        return in_array(
            $order_status->status,
            [
                OrderStatus::$place_order_status,
                OrderStatus::$payment_done_status,
                OrderStatus::$reject_status,
                OrderStatus::$resubmit_status,
            ]
        );
    }

    public function canReject(): bool
    {
        $order_status = OrderStatus::where('order_id', $this->id)->latest()->first();
        return in_array(
            $order_status->status,
            [OrderStatus::$place_order_status, OrderStatus::$resubmit_status, OrderStatus::$payment_done_status]
        );
    }


    public function canAccept(): bool
    {
        $order_status = $this->statuses()->latest()->first();
        return !in_array(
            $order_status->status,
            [OrderStatus::$cancel_by_shop_status, OrderStatus::$cancel_by_customer_status]
        );
    }


    public function hasEnoughStock(): bool
    {
        if ($this->module->canStockEditable()) {
            foreach ($this->items as $item) {
                if ($item->productOption->stock < $item->quantity) {
                    return false;
                }
            }
        }
        return true;
    }


    public function canReady(): bool
    {
        $order_status = OrderStatus::where('order_id', $this->id)->latest()->first();
        return in_array($order_status->status, [
            OrderStatus::$accepted_status,
            OrderStatus::$accept_for_delivery_status,
            OrderStatus::$assign_delivery_boy_status,

        ]);
    }

    public function canDeliver(): bool
    {
        $order_status = OrderStatus::where('order_id', $this->id)->latest()->first();
        return $order_status->status == OrderStatus::$order_ready_status;
    }

    public function canPickup(): bool
    {
        $order_status = OrderStatus::where('order_id', $this->id)->latest()->first();
        return $order_status->status == OrderStatus::$order_ready_status;
    }


    public function deductStockForOrder(): void
    {
        if ($this->module->canStockEditable()) {
            foreach ($this->items as $item) {
                $po = $item->productOption;
                $po->stock -= $item->quantity;
                $po->save();
            }
        }
    }


    // ------------------------- Order Flow Actions ---------------------------------------------------//

    public function payByCustomerWallet()
    {
        $order_payment = OrderPayment::where('order_id', $this->id)->first();
        $wallet = CustomerWallet::where('customer_id', $this->customer_id)->first();
        $amount = $order_payment->total_payment;

        if ($wallet->balance >= $amount) {
            $transaction = new CustomerWalletTransaction();
            $transaction->added = false;
            $transaction->amount = $amount;
            $transaction->customer_wallet_id = $wallet->id;
            $transaction->order_id = $this->id;
            $wallet->balance = $wallet->balance - $amount;


            $order_status = new OrderStatus();
            $order_status->status = OrderStatus::$payment_done_status;
            $order_status->order_id = $this->id;

            $order_payment->payment = $amount;
            $order_payment->payment_status = OrderPayment::$paid_status;

            DB::transaction(function () use ($order_payment, $order_status, $wallet, $transaction) {
                $transaction->save();
                $wallet->save();
                $order_status->save();
                $order_payment->save();
            });
            $this->sendOrderNotificationToSeller(
                BusinessSetting::_get(BusinessSetting::$order_payment_done_for_seller_notification)
            );
        }
    }

    public function cancelByCustomer(mixed $description)
    {
        $status = new OrderStatus(
            [
                'status' => OrderStatus::$cancel_by_customer_status,
                'order_id' => $this->id,
                'description' => $description
            ]
        );
        $wallet = CustomerWallet::where('customer_id', $this->customer_id)->first();
        $payment = $this->payments()->latest()->first();
        $amount = $payment->total_payment;
        $this->complete = true;
        DB::transaction(function () use ($status, $wallet, $amount, $payment) {
            if ($payment->payment_status == OrderPayment::$paid_status) {
                $transaction = new CustomerWalletTransaction();
                $transaction->added = true;
                $transaction->amount = $amount;
                $transaction->customer_wallet_id = $wallet->id;
                $transaction->order_id = $this->id;
                $wallet->balance = $wallet->balance + $amount;
                $transaction->save();
            }
            $status->save();
            $wallet->save();
            $this->save();
        });
        $this->sendOrderNotificationToSeller(
            BusinessSetting::_get(BusinessSetting::$cancel_by_customer_for_seller_notification)
        );
        $status->save();
    }

    public function cancelByShop()
    {
        $status = new OrderStatus(['status' => OrderStatus::$cancel_by_shop_status, 'order_id' => $this->id,]);
        $wallet = CustomerWallet::where('customer_id', $this->customer_id)->first();
        $payment = $this->payments()->latest()->first();
        $amount = $payment->total_payment;
        $this->complete = true;

        DB::transaction(function () use ($status, $wallet, $amount, $payment) {
            if ($payment->payment_status == OrderPayment::$paid_status) {
                $transaction = new CustomerWalletTransaction();
                $transaction->added = true;
                $transaction->amount = $amount;
                $transaction->customer_wallet_id = $wallet->id;
                $transaction->order_id = $this->id;
                $wallet->balance = $wallet->balance + $amount;
                $transaction->save();
            }
            $status->save();
            $wallet->save();
            $this->save();
        });
        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$cancel_by_seller_for_customer_notification)
        );
        $status->save();
    }

    public function rejectOrder($description = null)
    {
        $status = new OrderStatus(
            ['status' => OrderStatus::$reject_status, 'order_id' => $this->id, 'description' => $description]
        );
        $status->save();
        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$order_reject_for_customer_notification)
        );
    }

    public function resubmit(mixed $description)
    {
        $status = new OrderStatus(
            ['status' => OrderStatus::$resubmit_status, 'order_id' => $this->id, 'description' => $description]
        );
        $status->save();
        $this->sendOrderNotificationToSeller(
            BusinessSetting::_get(BusinessSetting::$order_resubmit_for_seller_notification)
        );
    }

    public function accept($description = null)
    {
        $status = new OrderStatus(
            ['status' => OrderStatus::$accepted_status, 'order_id' => $this->id, 'description' => $description]
        );
        DB::transaction(function () use ($status) {
            $status->save();
            $this->deductStockForOrder();
        });


        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$order_accept_for_customer_notification)
        );
        $message = BusinessSetting::_get(BusinessSetting::$order_accept_for_customer_sms);
        if (strlen(trim($message)) != 0) {
            $message = $message . PHP_EOL . "Order OTP is: " . $this->otp . PHP_EOL . "Your order invoice is here: " . url(
                    '/customer/orders/' . $this->id . '/invoice?i_otp=' . $this->invoice_otp
                );
            $this->sendOrderSMSToCustomer($message);
        }
        $this->sendViaEmail();
    }

    public function setReadyAt($date)
    {
        $this->ready_at = $date;
        $this->save();
        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$order_estimate_time_change_for_customer_notification)
        );
    }

    public function ready($description = null)
    {
        $status = new OrderStatus(
            ['status' => OrderStatus::$order_ready_status, 'order_id' => $this->id, 'description' => $description]
        );
        $status->save();
        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$order_ready_for_customer_notification)
        );
        if ($this->delivery_boy_id != null && $this->order_type == Order::$delivery_type) {
            $this->sendOrderNotificationToDeliveryBoy(
                $this->delivery_boy_id,
                BusinessSetting::_get(BusinessSetting::$order_ready_for_delivery_boy_notification)
            );
        }

        ProcessAutoAssignDeliveryBoy::dispatchIf(BusinessSetting::isAutoAssignDeliveryBoy(), $this);
    }

    public function setAssignDeliveryBoy(int $delivery_boy_id, $description = null): bool
    {
        $this->assign_delivery_boy_id = $delivery_boy_id;
        $status = new OrderStatus(
            [
                'status' => OrderStatus::$assign_delivery_boy_status,
                'order_id' => $this->id,
                'description' => $description
            ]
        );
        $accept_status = null;
        if (!BusinessSetting::canDeliveryBoyRejectOrder()) {
            $accept_status = new OrderStatus(
                [
                    'status' => OrderStatus::$accept_for_delivery_status,
                    'order_id' => $this->id,
                    'description' => $description
                ]
            );
            $this->delivery_boy_id = $delivery_boy_id;
        }
        $order = $this;
        try {
            DB::transaction(function () use ($accept_status, $order, $status) {
                $status->save();
                $order->save();
                $accept_status?->save();
            });
            if ($accept_status != null) {
                $this->sendOrderNotificationToCustomer(
                    BusinessSetting::_get(BusinessSetting::$delivery_boy_accept_for_customer_notification)
                );
            }
            $this->sendOrderNotificationToDeliveryBoy(
                $this->assign_delivery_boy_id,
                BusinessSetting::_get(BusinessSetting::$assign_order_for_delivery_boy_notification)
            );
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function rejectForDelivery($description = null)
    {
        $this->assign_delivery_boy_id = null;
        $this->save();
        $status = new OrderStatus(
            [
                'status' => OrderStatus::$reject_for_delivery_status,
                'order_id' => $this->id,
                'description' => $description
            ]
        );
        $status->save();
        $this->sendOrderNotificationToSeller(
            BusinessSetting::_get(BusinessSetting::$delivery_boy_reject_for_seller_notification)
        );
    }

    public function acceptForDelivery($description = null)
    {
        $this->delivery_boy_id = $this->assign_delivery_boy_id;

        $status = new OrderStatus(
            [
                'status' => OrderStatus::$accept_for_delivery_status,
                'order_id' => $this->id,
                'description' => $description
            ]
        );
        $status->save();
        $this->save();
        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$delivery_boy_accept_for_customer_notification)
        );
        $this->sendOrderNotificationToSeller(
            BusinessSetting::_get(BusinessSetting::$delivery_boy_accept_for_seller_notification)
        );
    }

    public function pickupByDelivery($description = null)
    {
        $status = new OrderStatus(
            ['status' => OrderStatus::$on_the_way_status, 'order_id' => $this->id, 'description' => $description]
        );
        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$out_for_delivery_customer_notification)
        );
        $status->save();
    }


    public function deliverByDeliveryBoy($description = null)
    {
        $status = new OrderStatus(
            ['status' => OrderStatus::$delivered_status, 'order_id' => $this->id, 'description' => $description]
        );

        $order = $this;
        DB::transaction(function () use ($status, $order) {
            $status->save();
            $order->makeComplete();
        });
        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$order_delivered_for_customer_notification)
        );
        $this->sendOrderNotificationToSeller(
            BusinessSetting::_get(BusinessSetting::$order_delivered_for_seller_notification)
        );
    }

    public function deliverByShop($description = null)
    {
        $status = new OrderStatus(
            ['status' => OrderStatus::$delivered_status, 'order_id' => $this->id, 'description' => $description]
        );
        $order = $this;
        DB::transaction(function () use ($status, $order) {
            $status->save();
            $order->makeComplete();
        });
        $this->sendOrderNotificationToCustomer(
            BusinessSetting::_get(BusinessSetting::$order_delivered_for_customer_notification)
        );
    }

    public function pickupByCustomer($description = null)
    {
        $status = new OrderStatus(
            ['status' => OrderStatus::$delivered_status, 'order_id' => $this->id, 'description' => $description]
        );
        $order = $this;
        DB::transaction(function () use ($status, $order) {
            $status->save();
            $order->makeComplete();
        });
        $this->sendOrderNotificationToSeller(
            BusinessSetting::_get(BusinessSetting::$order_delivered_for_seller_notification)
        );
    }

    //Other actions

    public function setAsPaid($description = null)
    {
        $payment = new OrderPayment(
            [
                'payment_type' => OrderPayment::$cash_on_delivery_type,
                'payment_status' => OrderPayment::$paid_status,
                'total_payment' => $this->total,
                'payment' => $this->total,
                'order_id' => $this->id
            ]
        );
        $payment->save();
    }


    // ------------------------- Common Functionalities ---------------------------------------------------//


    public function sendOrderNotificationToDeliveryBoy($id, $message)
    {
        $notification = new Notification(
            [
                'title' => 'Order #' . $this->id,
                'body' => $message,
                'notifiable_type' => DeliveryBoy::class,
                'notifiable_id' => $id,
                'data' => ['type' => Notification::$order_type, 'order_id' => $this->id,]
            ]
        );

        $notification->save();
        $notification->send_notification();
    }

    public function sendOrderNotificationToSeller($message)
    {
        $seller = Seller::where('shop_id', $this->shop_id)->where('is_owner', true)->first();
        if ($seller) {
            $notification = new Notification(
                [
                    'title' => 'Order #' . $this->id,
                    'body' => $message,
                    'notifiable_type' => Seller::class,
                    'notifiable_id' => $seller->id,
                    'data' => ['type' => Notification::$order_type, 'order_id' => $this->id,]
                ]
            );

            $notification->save();
            $notification->send_notification();
        }
    }

    public function sendOrderNotificationToAdmin($message)
    {
        $admin = Admin::first();
        $a_notification = new Notification(
            [
                'title' => 'Order #' . $this->id,
                'body' => $message,
                'notifiable_type' => Admin::class,
                'notifiable_id' => $admin->id,
                'data' => ['type' => Notification::$order_type, 'order_id' => $this->id,]
            ]
        );

        $a_notification->save();
        $a_notification->send_notification();
    }

    public function sendOrderNotificationToCustomer($message)
    {
        $notification = new Notification(
            [
                'title' => 'Order #' . $this->id,
                'body' => $message,
                'notifiable_type' => Customer::class,
                'notifiable_id' => $this->customer_id,
                'data' => ['type' => Notification::$order_type, 'order_id' => $this->id,]
            ]
        );

        $notification->save();
        $notification->send_notification();
    }

    public function sendOrderSMSToCustomer($message)
    {
        $message = new ShortMessage(
            [
                'message' => $message,
                'messageable_type' => Customer::class,
                'messageable_id' => $this->customer_id,
                'type' => ShortMessage::$order_type
            ]
        );

        $message->save();
        $message->send_sms();
    }


    public function sendViaEmail()
    {
        ProcessOrderInfoMail::dispatch($this);
    }


    // ------------------------- Order Completion ---------------------------------------------------//


    public function makeComplete(): bool
    {
        if ($this->complete) {
            return true;
        }
        $delivery_boy_id = $this->delivery_boy_id;
        if ($this->order_type == Order::$delivery_type && $delivery_boy_id == null) {
            return false;
        }
        $delivery_boy = null;

        $delivery_boy_revenue_amount = 0;
        $shop_delivery_revenue = 0;
        $admin_delivery_revenue = 0;

        if ($delivery_boy_id != null) {
            $delivery_boy = DeliveryBoy::findOrFail($delivery_boy_id);
            if ($delivery_boy->shop_id != null) {
                if ($delivery_boy->salary_based) {
                    $shop_delivery_revenue = $this->delivery_charge;
                } else {
                    $delivery_boy_revenue_amount = $this->delivery_charge;
                }
            } else {
                if ($delivery_boy->salary_based) {
                    $admin_delivery_revenue = $this->delivery_charge;
                } else {
                    $delivery_boy_revenue_amount = $this->delivery_charge;
                }
            }
        }

        $shop_revenue_amount = $this->order_amount + $this->tax + $this->packaging_charge + $shop_delivery_revenue;
        $admin_revenue_amount = $this->total - $delivery_boy_revenue_amount - $shop_revenue_amount;
        $total = $this->total;

        $this->admin_revenue_amount = $admin_revenue_amount;
        $this->shop_revenue_amount = $shop_revenue_amount;
        $this->delivery_boy_revenue_amount = $delivery_boy_revenue_amount;

        $admin_revenue = new AdminRevenue();
        $shop_revenue = new ShopRevenue();
        $delivery_boy_revenue = new DeliveryBoyRevenue();


        $payment = $this->payments()->latest()->first();
        if ($payment->payment_type == OrderPayment::$cash_on_delivery_type) {
            $payment->payment = $payment->total_payment;
            $payment->payment_status = OrderPayment::$paid_status;
        }


        // ------------------------------- Admin, Shop, Delivery Revenue Calculations : START ----------------------------------- //
        if ($delivery_boy != null && $this->order_type == Order::$delivery_type) {
            if ($payment->payment_type == OrderPayment::$cash_on_delivery_type) { // IF COD
                $delivery_boy_revenue->collected_payment_from_customer = $total;
                if ($delivery_boy->shop_id != null) {                          // If Shop Delivery

                    $admin_revenue->take_from_shop = $admin_revenue_amount;
                    $shop_revenue->take_from_delivery_boy = $shop_revenue_amount + $admin_revenue_amount;
                    $shop_revenue->pay_to_admin = $admin_revenue_amount;
                    $delivery_boy_revenue->pay_to_shop = $shop_revenue_amount + $admin_revenue_amount;
                } else {                                                  // If Delivery is Global
                    $admin_revenue->take_from_delivery_boy = $admin_revenue_amount + $shop_revenue_amount;
                    $admin_revenue->pay_to_shop = $shop_revenue_amount;
                    $shop_revenue->take_from_admin = $shop_revenue_amount;
                    $delivery_boy_revenue->pay_to_admin = $shop_revenue_amount + $admin_revenue_amount;
                }
            } else {                                                        // IF Pay Online
                $admin_revenue->collected_payment_from_customer = $total;

                if ($delivery_boy->shop_id != null) {                          // If Shop Delivery
                    $admin_revenue->pay_to_shop = $shop_revenue_amount + $delivery_boy_revenue_amount;
                    $shop_revenue->take_from_admin = $shop_revenue_amount + $delivery_boy_revenue_amount;
                    $shop_revenue->pay_to_delivery_boy = $delivery_boy_revenue_amount;
                    $delivery_boy_revenue->take_from_shop = $delivery_boy_revenue_amount;
                } else {
                    $admin_revenue->pay_to_shop = $shop_revenue_amount;// If Delivery is Global
                    $admin_revenue->pay_to_delivery_boy = $delivery_boy_revenue_amount;
                    $shop_revenue->take_from_admin = $shop_revenue_amount;
                    $delivery_boy_revenue->take_from_admin = $delivery_boy_revenue_amount;
                }
            }
        } else {                                                          //IF order is Self Pickup or POS

            if ($payment->payment_type == OrderPayment::$wallet) {      // IF Pay Online
                $admin_revenue->collected_payment_from_customer = $total;

                $admin_revenue->pay_to_shop = $shop_revenue_amount;
                $shop_revenue->take_from_admin = $shop_revenue_amount;
            } else {                                                              // IF Pay Cash, card or COD at shop
                $shop_revenue->collected_payment_from_customer = $total;
                $admin_revenue->take_from_shop = $admin_revenue_amount;
                $shop_revenue->pay_to_admin = $admin_revenue_amount;
            }
        }

        // --------- Assign Other entities to Admin, Shop, Delivery : START ----------------------------------- //


        $admin_revenue->revenue = $admin_revenue_amount;
        $admin_revenue->delivery_charge = $admin_delivery_revenue;
        $admin_revenue->delivery_commission = $this->delivery_commission;
        $admin_revenue->order_commission = $this->order_commission;
        $admin_revenue->coupon_discount = $this->coupon_discount;
        $admin_revenue->payment_charge = $this->payment_charge;
        $admin_revenue->shop_id = $this->shop_id;
        $admin_revenue->delivery_boy_id = $delivery_boy_id;
        $admin_revenue->module_id = $this->module_id;
        $admin_revenue->order_id = $this->id;


        $shop_revenue->revenue = $shop_revenue_amount;
        $shop_revenue->order_amount = $this->order_amount;
        $shop_revenue->tax = $this->tax;
        $shop_revenue->packaging_charge = $this->packaging_charge;
        $shop_revenue->delivery_charge = $shop_delivery_revenue;
        $shop_revenue->order_id = $this->id;
        $shop_revenue->delivery_boy_id = $delivery_boy_id;
        $shop_revenue->shop_id = $this->shop_id;


        $delivery_boy_revenue->revenue = $delivery_boy_revenue_amount;
        $delivery_boy_revenue->order_id = $this->id;
        $delivery_boy_revenue->shop_id = $this->shop_id;
        $delivery_boy_revenue->delivery_boy_id = $this->delivery_boy_id;

        // --------- Assign Other entities to Admin, Shop, Delivery : END ----------------------------------- //


        $this->load(['items', 'items.product', 'customer']);

        $customer = Customer::where('referral', $this->customer->from_referral)->first();
        $referral_amount = 0;
        if ($customer != null) {
            if (Order::where('customer_id', $this->customer_id)->count() != 1) {
                $customer = null;
            } else {
                $referral_amount = BusinessSetting::getCustomerReferralAmount();
            }
        }

        $this->complete = true;
        DB::transaction(
            function () use (
                $referral_amount,
                $customer,
                $payment,
                $delivery_boy_id,
                $shop_revenue,
                $admin_revenue,
                $delivery_boy_revenue
            ) {
                $this->save();
                $payment->save();
                if ($delivery_boy_id != null) {
                    $delivery_boy_revenue->save();
                }
                foreach ($this->items as $item) {
                    $product = $item->product;
                    $product->selling_count = $product->selling_count + $item->quantity;
                    $product->save();
                }
                if ($customer != null) {
                    CustomerWallet::addMoney($customer->id, $referral_amount, CustomerWalletTransaction::$referral);
                }
                if (BusinessSetting::isOrderLoyaltyEnabled()) {
                    CustomerLoyaltyWallet::addFromOrder($this);
                }
                $shop_revenue->save();
                $admin_revenue->save();
            }
        );

        return true;
    }


    // ------------------------- Relationships ---------------------------------------------------//


    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function module(): BelongsTo
    {
        return $this->BelongsTo(Module::class);
    }

    public function medias(): HasMany
    {
        return $this->hasMany(OrderMedia::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id', 'id');
    }

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class,);
    }

    public function assignDeliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class,);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(OrderStatus::class,);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->otp = rand(100000, 999999);
            $model->invoice_otp = StringUtil::generateRandomString(8);
        });
    }


}
