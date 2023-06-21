<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $review
 * @property mixed $rating
 * @property mixed $customer_id
 * @property mixed $shop_id
 * @property mixed $id
 * @property mixed $updated_at
 */
class StrictShopReviewResource extends JsonResource
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
            'shop_id' => $this->shop_id,
            'customer_id' => $this->customer_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'updated_at' => $this->updated_at,
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'customer' => new StrictCustomerResource($this->whenLoaded('customer'))
        ];


    }


}
