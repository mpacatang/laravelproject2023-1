<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *@property mixed $addon_id
 * @property mixed $quantity
 * @property mixed $id
 * @property mixed $order_item_id
 * @property mixed $price
 */
class StrictOrderItemAddonResource extends JsonResource
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
            'order_item_id' => $this->order_item_id,
            'addon_id' => $this->addon_id,
            'order_item' => new StrictOrderItemResource($this->whenLoaded('orderItem')),
            'addon' => new StrictAddonResource($this->whenLoaded('addon')),
        ];


    }


}
