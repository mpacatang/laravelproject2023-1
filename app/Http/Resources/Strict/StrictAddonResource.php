<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ResourceUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @property mixed $active
 * @property mixed $price
 * @property mixed $description
 * @property mixed $name
 * @property mixed $id
 * @property mixed $image
 * @property mixed $shop_id
 */
class StrictAddonResource extends JsonResource
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
            'id'=> $this->id,
            'name'=> $this->name,
            'description'=>$this->description,
            'price'=> $this->price,
            'shop_id'=> $this->shop_id,
            'active'=>$this->active,
            'image' => ResourceUtil::getImagePath($this->image),
            'shop' => new StrictAddonResource($this->whenLoaded('shop')),
        ];


    }


}
