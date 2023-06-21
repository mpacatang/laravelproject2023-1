<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $close_at
 * @property mixed $open_at
 * @property mixed $day
 * @property mixed $id
 * @property mixed $shop_id
 */
class StrictShopTimeResource extends JsonResource
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
            'day' => $this->day,
            'open_at' => $this->open_at,
            'close_at' => $this->close_at,
            'shop_id' => $this->shop_id,
            'shop' => new StrictShopResource($this->whenLoaded('shop'))

        ];
    }
}
