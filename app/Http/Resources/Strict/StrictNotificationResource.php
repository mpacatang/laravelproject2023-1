<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *@property mixed $created_at
 * @property mixed $data
 * @property mixed $body
 * @property mixed $title
 * @property mixed $id
 */
class StrictNotificationResource extends JsonResource
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
            'title'=>$this->title,
            'body'=>$this->body,
            'data'=>$this->data,
            'created_at' => $this->created_at
        ];


    }


}
