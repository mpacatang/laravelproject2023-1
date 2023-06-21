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
 * @property mixed $order_id
 * @property mixed $discount_type
 * @property mixed $discount
 * @property mixed $calculated_price
 * @property mixed $price
 * @property mixed $product_option_id
 * @property mixed $product_variant_id
 */
class StrictOrderItemResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product_option_id' => $this->product_option_id,
            'order_id' => $this->order_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'calculated_price' => $this->calculated_price,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'product' => new StrictProductResource($this->whenLoaded('product')),
            'product_option' => new StrictProductOptionResource($this->whenLoaded('productOption')),
            'addons'=> StrictOrderItemAddonResource::collection($this->whenLoaded('addons'))
        ];


    }


}
