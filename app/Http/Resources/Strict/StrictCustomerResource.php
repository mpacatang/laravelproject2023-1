<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ResourceUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $mobile_number_verified_at
 * @property mixed $email_verified_at
 * @property mixed $mobile_number
 * @property mixed $email
 * @property mixed $last_name
 * @property mixed $first_name
 * @property mixed $id
 * @property mixed $referral
 * @property mixed $avatar
 * @property mixed $active
 */
class StrictCustomerResource extends JsonResource
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
            'active' => $this->active,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'email_verified_at' => $this->email_verified_at,
            'referral' => $this->referral,
            'mobile_number_verified_at' => $this->mobile_number_verified_at,
            'avatar' => ResourceUtil::getImagePath($this->avatar),

        ];
    }


}
