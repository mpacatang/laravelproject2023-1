<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $customer_id
 * @property mixed $balance
 * @property mixed $id
 */
class StrictCustomerWalletResource extends JsonResource
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
            'balance' => $this->balance,
            'customer_id' => $this->customer_id,
            'transactions' => StrictCustomerWalletTransactionResource::collection($this->whenLoaded('transactions'))
        ];
    }


}
