<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $products
 * @property mixed $price
 * @property mixed $sub_title
 * @property mixed $title
 * @property mixed $id
 * @property mixed $admin_commission_type
 * @property mixed $admin_commission
 * @property mixed $active
 */
class StrictShopPlanResource extends JsonResource
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
            'title' => $this->title,
            'sub_title' => $this->sub_title,
            'price' => $this->price,
            'admin_commission' => $this->admin_commission,
            'admin_commission_type' => $this->admin_commission_type,
            'products' => $this->products,
        ];


    }


}
