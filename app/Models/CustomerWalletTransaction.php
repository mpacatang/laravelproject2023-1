<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $order_id
 * @property mixed $customer_wallet_id
 * @property mixed $amount
 * @property false|mixed $added
 * @property mixed|string $payment_method
 * @property mixed|string|null $payment_id
 */
class CustomerWalletTransaction extends BaseModel
{
    //===================== Defaults  ====================================//

    public static string $stripe_method = 'stripe';
    public static string $razorpay_method = 'razorpay';
    public static string $cashfree_method = 'cashfree';
    public static string $paypal_method = 'paypal';
    public static string $flutterwave_method = 'flutterwave';
    public static string $paystack_method = 'paystack';
    public static string $from_admin_method = 'from_admin';
    public static string $converted_from_loyalty = 'converted_from_loyalty';
    public static string $referral = 'referral';

    protected $casts = [
        'added' => 'boolean',
    ];

}
