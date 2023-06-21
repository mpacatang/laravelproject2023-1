<?php

namespace App\Models;

use App\Helpers\CResponse;
use App\Http\Resources\Strict\StrictOrderResource;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 */
class POS extends BaseModel
{

    public static function createOrder(Request $request): Response|StrictOrderResource|Application|ResponseFactory
    {
        $validated_data = $request->validate(
            [
                'items' => ['required', 'array'],
                'customer_id' => ['required'],
                'paid' => ['boolean'],
                'order_amount' => ['required', 'numeric'],
                'tax' => ['numeric'],
                'discount_amount' => ['numeric'],
                'total' => ['required', 'numeric'],
                'paid_amount' => ['numeric'],
                'change_amount' => ['numeric'],
                'payment_type' => ['required', 'in:cash,card'],
                'shop_id' => ['required']
            ]
        );

        $items = $request->get('items');
        $customer_id = $request->get('customer_id');
        $paid = $request->get('paid') ?? false;

        if (count($items) == 0) {
            return CResponse::error('Add items in POS');
        }

        $shop = Shop::with('module')->findOrFail($request->shop_id);

        $order = new Order();
        $order->delivery_charge = 0;
        $order->module_id = $shop->module->id;
        $order->order_amount = $validated_data['order_amount'];
        $order->coupon_discount = $validated_data['discount_amount'];
        $order->tax = $validated_data['tax'];
        $order->paid_amount = $validated_data['paid_amount'];
        $order->change_amount = $validated_data['change_amount'];
        $order->total = $validated_data['total'];

        $order->shop_id = $validated_data['shop_id'];
        $order->customer_id = $customer_id;
        $order->order_type = Order::$pos;

        $order_status = new OrderStatus();
        $order_status->status = OrderStatus::$accepted_status;

        $order_payment = new OrderPayment();
        $order_payment->payment_type = $validated_data['payment_type'];
        $order_payment->total_payment = $validated_data['total'];
        $order_payment->payment_status = $paid ? OrderPayment::$paid_status : OrderPayment::$unpaid_status;


        DB::transaction(function () use ($items, $order_payment, $order_status, $request, $order) {
            $order->save();

            foreach ($items as $item) {
                $product_option_id = $item['product_option_id'];
                $quantity = $item['quantity'];

                $product_option = ProductOption::with(['product'])->findOrFail($product_option_id);

                $order_item = new OrderItem();
                $order_item->price = $product_option->price;
                $order_item->calculated_price = $product_option->calculated_price;
                $order_item->discount = $product_option->discount;
                $order_item->discount_type = $product_option->discount_type;
                $order_item->quantity = $quantity;
                $order_item->product_id = $product_option->product_id;
                $order_item->product_option_id = $product_option_id;
                $order_item->order_id = $order->id;
                $order_item->save();

                if (isset($item['addons'])) {
                    $c_addons = $item['addons'];
                    foreach ($c_addons as $c_addon) {
                        $addon_id = $c_addon['addon_id'];
                        $a_quantity = $c_addon['quantity'];
                        $addon = Addon::findOrFail($addon_id);
                        $order_item_addon = new OrderItemAddon();
                        $order_item_addon->price = $addon->price;
                        $order_item_addon->quantity = $a_quantity;
                        $order_item_addon->addon_id = $addon_id;
                        $order_item_addon->order_item_id = $order_item->id;
                        $order_item_addon->save();
                    }
                }
            }

            $order_status->order_id = $order->id;
            $order_payment->order_id = $order->id;
            $order_status->save();
            $order_payment->save();
        });

        $order->loadAll();
        return new StrictOrderResource($order);
    }
}
