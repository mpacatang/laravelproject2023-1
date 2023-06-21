<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $product_id
 * @property mixed $shop_id
 * @property mixed $name
 * @property mixed $id
 * @property mixed $unit_id
 * @property mixed $unit_title
 */
class StrictProductVariantResource extends JsonResource
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
            'products' => StrictProductResource::collection($this->whenLoaded('products')),
        ];


    }


}
