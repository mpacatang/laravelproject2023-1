<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $expired_at
 * @property mixed $max_discount
 * @property mixed $min_order
 * @property mixed $discount_type
 * @property mixed $discount
 * @property mixed $description
 * @property mixed $code
 * @property mixed $id
 * @property mixed $active
 * @property mixed $started_at
 * @property mixed $shop_id
 * @property mixed $global
 * @property mixed $limit
 * @property mixed $first_order
 * @property mixed $delivery_free
 * @property mixed $zone_id
 * @property mixed $module_id
 */
class StrictCouponResource extends JsonResource
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
            'active' => $this->active,
            'code' => $this->code,
            'description' => $this->description,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'min_order' => $this->min_order,
            'max_discount' => $this->max_discount,
            'delivery_free' => $this->delivery_free,
            'first_order' => $this->first_order,
            'limit' => $this->limit,
            'started_at' => $this->started_at,
            'shop_id' => $this->shop_id,
            'module_id' => $this->module_id,
            'zone_id' => $this->zone_id,
            'expired_at' => $this->expired_at,
        ];


    }


}
