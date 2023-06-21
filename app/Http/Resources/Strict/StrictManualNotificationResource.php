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
 * @property mixed $schedule_at
 * @property mixed $all_delivery_boys
 * @property mixed $all_sellers
 * @property mixed $all_customers
 */
class StrictManualNotificationResource extends JsonResource
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
            'all_customers'=>$this->all_customers,
            'all_sellers'=>$this->all_sellers,
            'all_delivery_boys'=>$this->all_delivery_boys,
            'schedule_at' => $this->schedule_at,
            'created_at' => $this->created_at
        ];


    }


}
