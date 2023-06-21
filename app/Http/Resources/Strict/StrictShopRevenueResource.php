<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $created_at
 * @property mixed $order_id
 * @property mixed $shop_id
 * @property mixed $paid
 * @property mixed $pay_to_admin
 * @property mixed $revenue
 * @property mixed $id
 * @property mixed $shop_payout_id
 * @property mixed $take_from_delivery_boy
 * @property mixed $pay_to_delivery_boy
 * @property mixed $take_from_admin
 * @property mixed $delivery_charge
 * @property mixed $tax
 * @property mixed $order_amount
 * @property mixed $collected_payment_from_customer
 * @property mixed $packaging_charge
 */
class StrictShopRevenueResource extends JsonResource
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
            'order_amount' => $this->order_amount,
            'tax' => $this->tax,
            'packaging_charge' => $this->packaging_charge,
            'delivery_charge' => $this->delivery_charge,
            'revenue' => $this->revenue,
            'pay_to_admin' => $this->pay_to_admin,
            'take_from_admin' => $this->take_from_admin,
            'pay_to_delivery_boy' => $this->pay_to_delivery_boy,
            'take_from_delivery_boy' => $this->take_from_delivery_boy,
            'collected_payment_from_customer' => $this->collected_payment_from_customer,
            'shop_id' => $this->shop_id,
            'delivery_boy_id' => $this->shop_id,
            'shop_payout_id' => $this->shop_payout_id,
            'order_id' => $this->order_id,
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'delivery_boy' => new StrictDeliveryBoyResource($this->whenLoaded('deliveryBoy')),
            'shop_payout' => new StrictShopPayoutResource($this->whenLoaded('shopPayout')),
            'order' => new StrictOrderResource($this->whenLoaded('order')),
            'created_at' => $this->created_at,
        ];


    }


}
