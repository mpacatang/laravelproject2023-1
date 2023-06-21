<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $created_at
 * @property mixed $till_date
 * @property mixed $amount
 * @property mixed $id
 * @property mixed $shop_id
 * @property mixed $take_from_shop
 * @property mixed $pay_to_shop
 * @property mixed $take_from_admin
 * @property mixed $pay_to_admin
 */
class StrictDeliveryBoyPayoutResource extends JsonResource
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
            'pay_to_shop' => $this->pay_to_shop,
            'take_from_shop' => $this->take_from_shop,
            'pay_to_admin' => $this->pay_to_admin,
            'take_from_admin' => $this->take_from_admin,
            'till_date' => $this->till_date,
            'created_at' => $this->created_at,
            'shop_id' => $this->shop_id,
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'delivery_boy' => new StrictDeliveryBoyResource($this->whenLoaded('deliveryBoy')),
        ];


    }


}
