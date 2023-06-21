<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictProductOptionResource;
use Illuminate\Http\Request;

/**
 * @property mixed $product_variant_id
 * @property mixed $option_value
 * @property mixed $discount_type
 * @property mixed $discount
 * @property mixed $price
 * @property mixed $stock
 * @property mixed $barcode
 * @property mixed $sku
 * @property mixed $id
 */

class ProductOptionResource extends StrictProductOptionResource
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
            ...parent::toArray($request)
        ];


    }


}
