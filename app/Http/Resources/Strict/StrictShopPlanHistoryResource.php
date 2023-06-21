<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $products
 * @property mixed $price
 * @property mixed $sub_title
 * @property mixed $title
 * @property mixed $id
 * @property mixed $shop_id
 * @property mixed $shop_plan_id
 * @property mixed $created_at
 * @property mixed $ended_at
 * @property mixed $started_at
 * @property mixed $duration
 */
class StrictShopPlanHistoryResource extends JsonResource
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
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'duration' => $this->duration,
            'price' => $this->price,
            'shop_plan_id' => $this->shop_plan_id,
            'shop_id' => $this->shop_id,
            'created_at'=>$this->created_at,
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'shop_plan' => new StrictShopPlanResource($this->whenLoaded('shopPlan')),
        ];


    }


}
