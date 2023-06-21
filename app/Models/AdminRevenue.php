<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $revenue
 * @property mixed $order_id
 * @property float|mixed $payment_charge
 * @property float|mixed $shop_id
 * @property int|mixed $coupon_discount
 * @property float|mixed $order_commission
 * @property float|mixed $delivery_commission
 * @property int|mixed $delivery_charge
 * @property int|mixed $delivery_boy_id
 * @property int|mixed $module_id
 * @property float|int|mixed $take_from_shop
 * @property float|int|mixed $pay_to_shop
 * @property DeliveryBoyRevenue|mixed $pay_to_delivery_boy
 * @property float|int|mixed $take_from_delivery_boy
 * @property mixed $collected_payment_from_customer
 */
class AdminRevenue extends BaseModel
{

    //===================== RelationShips  ====================================//


    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class,);
    }

}
