<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ResourceUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 *@property mixed $shop_id
 * @property mixed $mobile_number
 * @property mixed $email
 * @property mixed $last_name
 * @property mixed $first_name
 * @property mixed $id
 * @property mixed $role_id
 * @property mixed $avatar
 * @property mixed $active
 * @property mixed $account_number
 * @property mixed $bank_name
 * @property mixed $is_owner
 */
class StrictSellerResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'is_owner' => $this->is_owner,
            'mobile_number' => $this->mobile_number,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'role_id' => $this->role_id,
            'shop_id' => $this->shop_id,
            'avatar' => ResourceUtil::getImagePath($this->avatar),

            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'role' => new StrictSellerRoleResource($this->whenLoaded('role')),
        ];
    }
}
