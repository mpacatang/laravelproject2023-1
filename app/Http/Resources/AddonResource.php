<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictAddonResource;
use Illuminate\Http\Request;

/**

 */
class AddonResource extends StrictAddonResource
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
            'shop' => new ShopResource($this->whenLoaded('shop')),
        ];


    }


}
