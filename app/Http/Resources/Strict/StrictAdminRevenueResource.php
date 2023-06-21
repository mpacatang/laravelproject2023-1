<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $created_at
 * @property mixed $order_id
 * @property mixed $shop_id
 * @property mixed $payout
 * @property mixed $revenue
 * @property mixed $id
 * @property mixed $collected_payment_from_customer
 * @property mixed $payment_charge
 * @property mixed $coupon_discount
 * @property mixed $order_commission
 * @property mixed $delivery_commission
 * @property mixed $delivery_charge
 */
class StrictAdminRevenueResource extends JsonResource
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
            'revenue' => $this->revenue,
            'delivery_charge' => $this->delivery_charge,
            'delivery_commission' => $this->delivery_commission,
            'order_commission' => $this->order_commission,
            'coupon_discount' => $this->coupon_discount,
            'payment_charge' => $this->payment_charge,
            'collected_payment_from_customer' => $this->collected_payment_from_customer,
            'order_id' => $this->order_id,
            'created_at' => $this->created_at,
        ];
    }


}
