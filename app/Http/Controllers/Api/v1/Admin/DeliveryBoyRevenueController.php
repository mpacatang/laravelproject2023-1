<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryBoyRevenueResource;
use App\Models\DeliveryBoyRevenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class DeliveryBoyRevenueController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $delivery_boy_id = $request->query('delivery_boy_id');
        $till_date = $request->query('till_date');
        $delivery_boy_revenues = DeliveryBoyRevenue::with(['deliveryBoy']);
        if (isset($delivery_boy_id)) {
            $delivery_boy_revenues = $delivery_boy_revenues->where('delivery_boy_id', $delivery_boy_id);
        }
        if (isset($till_date)) {
            $delivery_boy_revenues = $delivery_boy_revenues->whereDate('created_at', '<=', Carbon::parse($till_date));
        }
        return StrictDeliveryBoyRevenueResource::collection($delivery_boy_revenues->get());
    }


}

