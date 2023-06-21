<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $review
 * @property mixed $rating
 * @property mixed $customer_id
 * @property mixed $shop_id
 * @property mixed $order_id
 * @property mixed $product_option_id
 * @property mixed $product_variant_id
 * @property mixed $product_id
 * @property mixed $id
 * @property mixed $updated_at
 */
class   StrictProductReviewResource extends JsonResource
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
            'product_option_id' => $this->product_option_id,
            'order_id' => $this->order_id,
            'shop_id' => $this->shop_id,
            'customer_id' => $this->customer_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'customer' => new StrictCustomerResource($this->whenLoaded('customer')),
            'product' => new StrictProductResource($this->whenLoaded('product')),
            'updated_at'=>$this->updated_at
        ];


    }


}
