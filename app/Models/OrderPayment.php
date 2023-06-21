<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $order_id
 * @property float|int|mixed $total_payment
 * @property mixed|string $payment_status
 * @property mixed|string $payment_type
 */
class OrderPayment extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    //Payment types
    public static string $cash_on_delivery_type = 'cash_on_delivery';
    public static string $wallet = 'wallet';
    public static string $cash = 'cash';
    public static string $card = 'card';

    //Payment statuses
    public static string $paid_status = 'paid';
    public static string $unpaid_status = 'unpaid';


    //===================== Rules  ====================================//
    public static function verifyRules($id = null,): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return ['payment_type' => ['required', 'in:cash_on_delivery,wallet']];
    }


}
