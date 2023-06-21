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
 */
class StrictCartResource extends JsonResource
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
            'quantity' => $this->quantity,
            'product_id' => $this->product_id,
            'shop_id' => $this->shop_id,
            'product' => new StrictProductResource($this->whenLoaded('product')),
            'product_option' => new StrictProductOptionResource($this->whenLoaded('productOption')),
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'addons' => StrictCartAddonResource::collection($this->whenLoaded('addons'))
        ];


    }


}
