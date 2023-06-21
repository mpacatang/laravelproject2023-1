<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $order_id
 * @property mixed $description
 * @property mixed|string $status
 */
class OrderStatus extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    //Statuses
    public static string $place_order_status = 'place_order';
    public static string $payment_done_status = 'payment_done';

    public static string $cancel_by_customer_status = 'cancel_by_customer';
    public static string $cancel_by_shop_status = 'cancel_by_shop';

    public static string $accepted_status = 'accepted';
    public static string $reject_status = 'reject';
    public static string $resubmit_status = 'resubmit';

    public static string $processing_status = 'processing';

    public static string $assign_delivery_boy_status = 'assign_delivery_boy';
    public static string $accept_for_delivery_status = 'accept_for_delivery';
    public static string $reject_for_delivery_status = 'reject_for_delivery';

    public static string $order_ready_status = 'order_ready';

    public static string $on_the_way_status = 'on_the_way';
    public static string $delivered_status = 'delivered';
    public static string $reviewed_status = 'reviewed';


    //======================= Getters ===========================================//

    public function isCancelled(): bool
    {
        return $this->status == self::$cancel_by_customer_status || $this->status == self::$cancel_by_shop_status;
    }

}
