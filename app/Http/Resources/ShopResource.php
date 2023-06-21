<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictShopResource;
use Illuminate\Http\Request;

/**
 */
class ShopResource extends StrictShopResource
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
            'sellers' => SellerResource::collection($this->whenLoaded('sellers')),
        ];
    }
}
