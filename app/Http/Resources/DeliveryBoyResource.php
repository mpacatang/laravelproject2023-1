<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictDeliveryBoyResource;
use Illuminate\Http\Request;

/**
 * @property mixed $email
 * @property mixed $name
 * @property mixed $id
 * @property mixed $avatar
 * @property mixed $last_name
 * @property mixed $first_name
 * @property mixed $mobile_number
 * @property mixed $active_for_delivery
 * @property mixed $vehicle_number
 * @property mixed $identity_type
 * @property mixed $identity_number
 * @property mixed $identity_image
 * @property mixed $longitude
 * @property mixed $latitude
 * @property mixed $active
 * @property mixed $account_number
 * @property mixed $bank_name
 */
class DeliveryBoyResource extends StrictDeliveryBoyResource
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
            'bank_name'=>$this->bank_name,
            'account_number'=>$this->account_number
        ];
    }


}
