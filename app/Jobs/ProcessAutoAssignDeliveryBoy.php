<?php

namespace App\Jobs;

use App\Helpers\Utils;
use App\Models\DeliveryBoy;
use App\Models\Order;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAutoAssignDeliveryBoy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public Order $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            if ($this->order->order_type == Order::$delivery_type && $this->order->assign_delivery_boy_id == null) {
                $shop = $this->order->shop;
                if ($shop->self_delivery) {
                    $delivery_boys = DeliveryBoy::where('active_for_delivery', true)
                        ->where('shop_id', $shop->id)
                        ->get();
                } else {
                    $delivery_boys = DeliveryBoy::where('active_for_delivery', true)
                        ->where('shop_id', null)->get();
                }

                $nearest_db_id = null;
                $nearest = PHP_INT_MAX;
                if (sizeof($delivery_boys) > 0) {
                    foreach ($delivery_boys as $delivery_boy) {
                        $lat1 = $shop->latitude;
                        $lng1 = $shop->longitude;
                        $lat2 = $delivery_boy->latitude;
                        $lng2 = $delivery_boy->longitude;
                        if ($lat2 && $lng2) {
                            $distance = Utils::distanceBetweenTwoLatLng($lat1, $lng1, $lat2, $lng2);
                            if ($distance < $nearest) {
                                $nearest_db_id = $delivery_boy->id;
                                $nearest = $distance;
                            }
                        }
                    }
                    if ($nearest_db_id != null) {
                        $this->order->setAssignDeliveryBoy($nearest_db_id, 'System assigned delivery');
                    }

                }

            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}
