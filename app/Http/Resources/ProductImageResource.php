<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @property mixed $image
 * @property mixed $id
 * @property mixed $product_id
 */
class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request => Product
     * @return array
     */


    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'image' => ResourceUtil::getImagePath($this->image),
            'product_id' => $this->product_id,
            'product' => new ProductResource($this->whenLoaded('product')),

        ];
    }
}
