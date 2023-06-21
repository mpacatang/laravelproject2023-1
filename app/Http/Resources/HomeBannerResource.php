<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictHomeBannerResource;
use Illuminate\Http\Request;

/**
 * @property mixed $product_id
 * @property mixed $shop_id
 * @property mixed $image
 * @property mixed $id
 * @property mixed $active
 */
class HomeBannerResource extends StrictHomeBannerResource
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
        ];
    }
}
