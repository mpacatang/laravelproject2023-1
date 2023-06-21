<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ResourceUtil;
use App\Http\Resources\ShopResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $product_id
 * @property mixed $shop_id
 * @property mixed $image
 * @property mixed $id
 * @property mixed $active
 */
class StrictHomeBannerResource extends JsonResource
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
            'image' => ResourceUtil::getImagePath($this->image),
            'shop_id' => $this->shop_id,
            'product_id' => $this->product_id,
            'shop' => new ShopResource($this->whenLoaded('shop')),
            'product' => new ShopResource($this->whenLoaded('product')),
        ];
    }
}
