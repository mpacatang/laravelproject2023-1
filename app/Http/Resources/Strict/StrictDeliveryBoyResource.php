<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ResourceUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
 * @property mixed $salary_based
 * @property mixed $zone_id
 * @property mixed $shop_id
 * @property mixed $approved
 * @property mixed $archived
 */
class StrictDeliveryBoyResource extends JsonResource
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
            'approved' => $this->approved,
            'archived' => $this->archived,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'salary_based'=>$this->salary_based,
            'avatar' => ResourceUtil::getImagePath($this->avatar),
            'active_for_delivery' => $this->active_for_delivery,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'mobile_number' => $this->mobile_number,
            'identity_number' => $this->identity_number,
            'identity_type' => $this->identity_type,
            'vehicle_number' => $this->vehicle_number,
            'shop_id' => $this->shop_id,
            'zone_id' => $this->zone_id,
            'identity_image' => ResourceUtil::getImagePath($this->identity_image),
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'zone' => new StrictZoneResource($this->whenLoaded('zone')),
        ];


    }


}
