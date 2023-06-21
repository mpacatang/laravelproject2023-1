<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopRevenueResource;
use App\Models\ShopRevenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ReportController extends Controller
{
    public function report(Request $request): AnonymousResourceCollection
    {
        $revenues = ShopRevenue::where('shop_id', $this->getShopId());

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        if ($start_date) {
            $start_date = Carbon::parse($start_date);
            $end_date = !$end_date ? Carbon::now() : Carbon::parse($end_date);
            $revenues->whereBetween('created_at', [$start_date, $end_date]);
        }

        return StrictShopRevenueResource::collection($revenues->get());
    }


}

