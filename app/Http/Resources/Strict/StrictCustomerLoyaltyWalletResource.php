<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $customer_id
 * @property mixed $id
 * @property mixed $point
 */
class StrictCustomerLoyaltyWalletResource extends JsonResource
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
            'point' => $this->point,
            'customer_id' => $this->customer_id,
            'transactions' => StrictCustomerLoyaltyTransactionResource::collection($this->whenLoaded('transactions'))
        ];
    }


}
