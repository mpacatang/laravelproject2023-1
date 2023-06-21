<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ResourceUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @property mixed $avatar
 * @property mixed $mobile_number
 * @property mixed $email
 * @property mixed $last_name
 * @property mixed $first_name
 * @property mixed $active
 * @property mixed $id
 * @property mixed $is_super
 * @property mixed $role_id
 */
class StrictAdminResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request => Product
     * @return array
     */


    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'active' => $this->active,
            'is_super' => $this->is_super,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'role_id' => $this->role_id,
            'avatar' => ResourceUtil::getImagePath($this->avatar),
            'role' => new StrictAdminRoleResource($this->whenLoaded('role')),

        ];
    }
}
