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
 * @property mixed $take_from_shop
 * @property mixed $pay_to_shop
 * @property mixed $delivery_boy_payout_id
 */
class StrictDeliveryBoyRevenueResource extends JsonResource
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
            'revenue' => $this->revenue,
            'pay_to_admin' => $this->pay_to_admin,
            'take_from_admin' => $this->take_from_admin,
            'pay_to_shop' => $this->pay_to_shop,
            'take_from_shop' => $this->take_from_shop,
            'collected_payment_from_customer' => $this->collected_payment_from_customer,
            'shop_id' => $this->shop_id,
            'delivery_boy_id' => $this->shop_id,
            'delivery_boy_payout_id' => $this->delivery_boy_payout_id,
            'order_id' => $this->order_id,
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'delivery_boy' => new StrictDeliveryBoyResource($this->whenLoaded('deliveryBoy')),
            'delivery_boy_payout' => new StrictShopPayoutResource($this->whenLoaded('deliveryBoyPayout')),
            'order' => new StrictOrderResource($this->whenLoaded('order')),
            'created_at' => $this->created_at,
        ];


    }


}
