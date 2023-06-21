<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $coordinates
 * @property mixed $name
 * @property mixed $id
 * @property mixed $active
 */
class StrictZoneResource extends JsonResource
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
            'active'=>$this->active,
            'name' => $this->name,
            'coordinates' => $this->coordinates,

        ];
    }


}
