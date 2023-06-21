<?php

namespace App\Models;

use App\Helpers\BusinessHelper;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property mixed $user_id
 * @property mixed $customer_id
 * @property mixed $id
 * @property mixed $point
 */
class CustomerLoyaltyWallet extends BaseModel
{

    //===================== Functionalities  ====================================//

    public static function addFromOrder(Order $order)
    {
        if ($order->customer_id) {
            $loyalty_amount = BusinessSetting::_get(BusinessSetting::$customer_order_loyalty_amount);
            $loyalty_amount_type = BusinessSetting::_get(BusinessSetting::$customer_order_loyalty_amount_type);
            $point = BusinessHelper::_calculate($order->total, $loyalty_amount, $loyalty_amount_type);

            $customer_wallet = CustomerLoyaltyWallet::with(['customer'])->findOrFail($order->customer_id);

            $transaction = new CustomerLoyaltyTransaction();
            $transaction->point = $point;
            $transaction->added = true;
            $transaction->order_id = $order->id;
            $transaction->customer_loyalty_wallet_id = $customer_wallet->id;

            $customer_wallet->point += $point;
            DB::transaction(function () use ($transaction, $customer_wallet) {
                $customer_wallet->save();
                $transaction->save();
            });
        }
    }

    public function convertPoint(float $point)
    {
        $multiplier = BusinessSetting::_get(BusinessSetting::$customer_loyalty_to_wallet_multiplier);
        if ($multiplier <= 0) {
            return;
        }
        $transaction = new CustomerLoyaltyTransaction();
        $amount = $point / $multiplier;

        $transaction->point = $point;
        $transaction->added = false;
        $transaction->customer_loyalty_wallet_id = $this->id;
        $this->point -= $point;
        DB::transaction(function () use ($amount, $transaction) {
            $this->save();
            $transaction->save();
            CustomerWallet::addMoney(
                $this->customer_id,
                $amount,
                CustomerWalletTransaction::$converted_from_loyalty,
                $transaction->id
            );
        });
    }

    //===================== RelationShips  ====================================//

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CustomerLoyaltyTransaction::class)->latest();
    }


}
