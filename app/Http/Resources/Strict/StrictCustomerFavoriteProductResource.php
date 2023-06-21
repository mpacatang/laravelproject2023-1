<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $customer_id
 * @property mixed $product_id
 * @property mixed $id
 */
class StrictCustomerFavoriteProductResource extends JsonResource
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
            'product_id' => $this->product_id,
            'customer_id' => $this->customer_id,
            'customer' => new StrictCustomerResource($this->whenLoaded('customer')),
        ];


    }


}
