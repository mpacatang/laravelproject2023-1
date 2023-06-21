<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $created_at
 * @property mixed $status
 * @property mixed $order_id
 * @property mixed $id
 * @property mixed $description
 */
class StrictOrderStatusResource extends JsonResource
{


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */


    public function toArray($request)
    {
//        return $this;
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at
        ];


    }


}
