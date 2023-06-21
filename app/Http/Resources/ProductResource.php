<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictProductResource;
use Illuminate\Http\Request;

/**

 *
 */
class ProductResource extends StrictProductResource
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
            'addons' => AddonResource::collection($this->whenLoaded('addons')),
        ];


    }


}
