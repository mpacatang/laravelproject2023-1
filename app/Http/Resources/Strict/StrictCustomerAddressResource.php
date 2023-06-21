<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *@property mixed $customer_id
 * @property mixed $latitude
 * @property mixed $longitude
 * @property mixed $pincode
 * @property mixed $city
 * @property mixed $landmark
 * @property mixed $address
 * @property mixed $type
 * @property mixed $id
 * @property mixed $selected
 */
class StrictCustomerAddressResource extends JsonResource
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
            'selected' => $this->selected,
            'type' => $this->type,
            'address' => $this->address,
            'landmark' => $this->landmark,
            'city' => $this->city,
            'pincode' => $this->pincode,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'customer_id' => $this->customer_id,
            'customer' => new StrictCustomerResource($this->whenLoaded('customer')),
        ];


    }


}
