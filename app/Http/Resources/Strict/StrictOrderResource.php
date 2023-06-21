<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $addon_id
 * @property mixed $product_id
 * @property mixed $quantity
 * @property mixed $id
 * @property mixed $shop_id
 * @property mixed $total
 * @property mixed $tax
 * @property mixed $coupon_discount
 * @property mixed $delivery_charge
 * @property mixed $order
 * @property mixed $otp
 * @property mixed $order_type
 * @property mixed $notes
 * @property mixed $created_at
 * @property mixed $order_status
 * @property mixed $ready_at
 * @property mixed $delivery_boy_id
 * @property mixed $assign_delivery_boy_id
 * @property mixed $payment_charge
 * @property mixed $delivery_commission
 * @property mixed $order_commission
 * @property mixed $delivery_boy_revenue
 * @property mixed $order_amount
 * @property mixed $packaging_charge
 * @property mixed $complete
 * @property mixed $invoice_otp
 */
class StrictOrderResource extends JsonResource
{


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */


    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'shop_id' => $this->shop_id,
            'complete' => $this->complete,
            'order_type' => $this->order_type,
            'otp' => $this->otp,
            'invoice_otp' => $this->invoice_otp,
            'order_amount' => $this->order_amount,
            'packaging_charge' => $this->packaging_charge,
            'tax' => $this->tax,
            'order_commission' => $this->order_commission,
            'delivery_charge' => $this->delivery_charge,
            'delivery_commission' => $this->delivery_commission,
            'coupon_discount' => $this->coupon_discount,
            'payment_charge' => $this->payment_charge,
            'total' => $this->total,
            'notes' => $this->notes,
            'ready_at' => $this->ready_at,
            'assign_delivery_boy_id' => $this->assign_delivery_boy_id,
            'delivery_boy_id' => $this->delivery_boy_id,
            'delivery_boy_revenue' => $this->delivery_boy_revenue,
            'items' => StrictOrderItemResource::collection($this->whenLoaded('items')),
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'payments' => StrictOrderPaymentResource::collection($this->whenLoaded('payments')),
            'delivery_boy' => new StrictDeliveryBoyResource($this->whenLoaded('deliveryBoy')),
            'assign_delivery_boy' => new StrictDeliveryBoyResource($this->whenLoaded('assignDeliveryBoy')),
            'customer' => new StrictCustomerResource($this->whenLoaded('customer')),
            'address' => new StrictCustomerAddressResource($this->whenLoaded('address')),
            'statuses' => StrictOrderStatusResource::collection($this->whenLoaded('statuses')),
            'medias' => StrictOrderMediaResource::collection($this->whenLoaded('medias')),
            'created_at' => $this->created_at
        ];


    }


}
