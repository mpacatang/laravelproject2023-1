<?php

namespace App\Http\Resources\Strict;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $addon_id
 * @property mixed $product_id
 * @property mixed $quantity
 * @property mixed $id
 * @property mixed $shop_id
 * @property mixed $order_id
 * @property mixed $payment_type
 * @property mixed $payment
 * @property mixed $total_payment
 * @property mixed $payment_status
 * @property mixed $payment_method
 */
class StrictOrderPaymentResource extends JsonResource
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
            'payment_type' => $this->payment_type,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'total_payment' => $this->total_payment,
            'payment' => $this->payment,
            'order_id' => $this->order_id,
        ];


    }


}
