<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $customer_wallet_id
 * @property mixed $amount
 * @property mixed $added
 * @property mixed $id
 * @property mixed $created_at
 *
 * @property mixed $order_id
 * @property mixed $payment_method
 */
class StrictCustomerWalletTransactionResource extends JsonResource
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
            'added' => $this->added,
            'amount' => $this->amount,
            'order_id' => $this->order_id,
            'created_at' => $this->created_at,
            'payment_method' => $this->payment_method,
            'customer_wallet_id' => $this->customer_wallet_id,
        ];
    }


}
