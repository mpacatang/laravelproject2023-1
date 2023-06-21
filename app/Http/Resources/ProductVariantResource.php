<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictProductVariantResource;
use Illuminate\Http\Request;

/**
 *@property mixed $product_id
 * @property mixed $shop_id
 * @property mixed $option_title
 * @property mixed $option_type
 * @property mixed $name
 * @property mixed $id
 */
class ProductVariantResource extends StrictProductVariantResource
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
