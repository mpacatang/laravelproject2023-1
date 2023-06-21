<?php

namespace App\Http\Resources\Strict;

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
 * @property mixed $customer_loyalty_wallet_id
 * @property mixed $point
 */
class StrictCustomerLoyaltyTransactionResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'added' => $this->added,
            'point' => $this->point,
            'order_id' => $this->order_id,
            'created_at' => $this->created_at,
            'customer_loyalty_wallet_id' => $this->customer_loyalty_wallet_id,
        ];
    }


}
