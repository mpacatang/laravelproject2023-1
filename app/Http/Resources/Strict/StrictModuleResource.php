<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $title
 * @property mixed $type
 * @property mixed $id
 * @property mixed $active
 * @property mixed $products_count
 * @property mixed $shops_count
 * @property mixed $shops
 * @property mixed $products
 */
class StrictModuleResource extends JsonResource
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
            'active' => $this->active,
            'type' => $this->type,
            'title' => $this->title,
            'shops_count' => $this->whenLoaded('shops', function() {
                return $this->shops->count();}),
            'products_count' =>  $this->whenLoaded('products', function() {
                return $this->products->count();}),
        ];
    }
}
