<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictCartResource;
use Illuminate\Http\Request;

/**
 * @property mixed $addon_id
 * @property mixed $product_id
 * @property mixed $quantity
 * @property mixed $id
 * @property mixed $shop_id
 */
class CartResource extends StrictCartResource
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
            ...parent::toArray($request),
            'shop' => new ShopResource($this->whenLoaded('shop')),
        ];
    }
}
