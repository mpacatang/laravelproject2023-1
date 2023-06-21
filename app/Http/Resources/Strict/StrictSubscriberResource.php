<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @property mixed $email
 * @property mixed $id
 * @property mixed $created_at
 */

class StrictSubscriberResource extends JsonResource
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
            'email' => $this->email,
            'created_at' => $this->created_at,
        ];
    }
}
