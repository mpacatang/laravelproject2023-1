<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $description
 * @property mixed $id
 * @property mixed $title
 * @property mixed $type
 * @property mixed $active
 */
class StrictUnitResource extends JsonResource
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
            'active' => $this->active,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,

        ];


    }


}
