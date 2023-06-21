<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
 * @property mixed $calculated_price
 * @property mixed $total_discount
 */
class StrictProductOptionResource extends JsonResource
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
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'stock' => $this->stock,
            'price' => $this->price,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'option_value' => $this->option_value,
            'product_variant_id' => $this->product_variant_id,
            'total_discount' => $this->total_discount,
            'calculated_price' => $this->calculated_price,

        ];


    }


}
