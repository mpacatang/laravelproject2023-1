<?php

namespace App\Jobs;

use App\Helpers\Utils;
use App\Models\Coupon;
use App\Models\DeliveryBoy;
use App\Models\Order;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCouponExpire implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public int $couponId;

    public function __construct($couponId)
    {
        $this->couponId = $couponId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {

        try {
            $coupon = Coupon::findOrFail($this->couponId);
//            $coupon->active = false;
            $coupon->saveQuietly();
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}
