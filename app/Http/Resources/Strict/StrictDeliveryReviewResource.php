<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $review
 * @property mixed $rating
 * @property mixed $customer_id
 * @property mixed $delivery_boy_id
 * @property mixed $order_id
 * @property mixed $id
 * @property mixed $updated_at
 */
class StrictDeliveryReviewResource extends JsonResource
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
            'order_id' => $this->order_id,
            'delivery_boy_id' => $this->delivery_boy_id,
            'customer_id' => $this->customer_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'updated_at'=> $this->updated_at,
            'delivery_boy' => new StrictDeliveryBoyResource($this->whenLoaded('deliveryBoy')),
            'customer' => new StrictCustomerResource($this->whenLoaded('customer'))
        ];


    }


}
