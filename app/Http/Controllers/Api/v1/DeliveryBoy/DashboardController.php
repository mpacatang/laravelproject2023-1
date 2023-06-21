<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoyRevenue;
use App\Models\Order;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index(Request $request): array
    {
        $data = [];


        $data['total_revenue'] = DeliveryBoyRevenue::where('delivery_boy_id', $this->getUserId())->sum('revenue');
        $data['order_count'] = Order::where('delivery_boy_id', $this->getUserId())->count();


        $data['revenues'] = $this->getRevenues($request);

        return $data;
    }

    public function getRevenues($request): array
    {
        $interval = $request->query('revenue_interval', 'monthly');

        $revenues = [];

        if ($interval == 'monthly') {
            $start = now()->modify("-11 month");
            $interval = new DateInterval('P1M');
            $end = now()->modify("+1 month");
            $period = new DatePeriod($start, $interval, $end);
            foreach ($period as $index => $dt) {
                $revenues[$index] = [
                    'date' => $dt->format('Y-m-d H:i:s'),
                    'revenue' => DeliveryBoyRevenue::where('delivery_boy_id', $request->user_id)->whereMonth(
                        'created_at',
                        $dt->format('m')
                    )
                        ->whereYear('created_at', $dt->format('Y'))
                        ->sum('revenue')
                ];
            }
        } else {
            $start = now()->modify("-30 days");
            $interval = new DateInterval('P1D');
            $end = now()->modify("+1 day");
            $period = new DatePeriod($start, $interval, $end);
            foreach ($period as $index => $dt) {
                $revenues[$index] = [
                    'date' => $dt->format('Y-m-d H:i:s'),
                    'revenue' => DeliveryBoyRevenue::where('delivery_boy_id', $request->user_id)->whereDate(
                        'created_at',
                        $dt
                    )
                        ->sum('revenue')
                ];
            }
        }
        return $revenues;
    }


    public function revenues(Request $request): array
    {
        return $this->getRevenues($request);
    }


}

