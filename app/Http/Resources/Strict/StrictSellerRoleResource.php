<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ShopResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $permissions
 * @property mixed $shop_id
 * @property mixed $id
 *@property mixed $title
 * @property mixed $active
 */
class StrictSellerRoleResource extends JsonResource
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
            'title' => $this->title,
            'shop_id' => $this->shop_id,
            'permissions' => $this->permissions,
            'shop' => new ShopResource($this->whenLoaded('shop')),
        ];
    }
}
