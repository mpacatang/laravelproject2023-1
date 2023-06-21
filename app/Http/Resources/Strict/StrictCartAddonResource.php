<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $addon_id
 * @property mixed $cart_id
 * @property mixed $quantity
 * @property mixed $id
 * @property mixed $price
 */
class StrictCartAddonResource extends JsonResource
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
            'quantity' => $this->quantity,
            'price' => $this->price,
            'cart_id' => $this->cart_id,
            'addon_id' => $this->addon_id,
            'cart' => new StrictCartResource($this->whenLoaded('cart')),
            'addon' => new StrictAddonResource($this->whenLoaded('addon')),
        ];


    }


}
