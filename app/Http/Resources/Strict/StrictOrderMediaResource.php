<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ResourceUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $order_id
 * @property mixed $id
 * @property mixed $media
 */
class StrictOrderMediaResource extends JsonResource
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
            'media' => ResourceUtil::getImagePath($this->media),
            'order_id' => $this->order_id,
            'order' => new StrictOrderResource($this->whenLoaded('order')),

        ];
    }
}
