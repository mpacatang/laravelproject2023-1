<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $order_id
 * @property mixed $customer_loyalty_wallet_id
 * @property mixed $point
 * @property false|mixed $added
 * @property mixed|string $payment_method
 * @property mixed|string|null $payment_id
 */
class CustomerLoyaltyTransaction extends BaseModel
{
    //===================== Defaults  ====================================//


    protected $casts = [
        'added' => 'boolean',
    ];

}
