<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminRevenueResource;
use App\Models\AdminRevenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ReportController extends Controller
{
    public function report(Request $request): AnonymousResourceCollection
    {
        $revenues = AdminRevenue::with('shop');

        $module_id = $request->get('module_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');
        $shop_id = $request->get('shop_id');


        if ($module_id) {
            $revenues->whereHas('shop', function ($q) use ($module_id) {
                $q->where('module_id', $module_id);
            });
        }

        if ($shop_id) {
            $revenues->where('shop_id', $shop_id);
        }

        if ($start_date) {
            $start_date = Carbon::parse($start_date);
            $end_date = !$end_date ? Carbon::now() : Carbon::parse($end_date);
            $revenues->whereBetween('created_at', [$start_date, $end_date]);
        }

        return StrictAdminRevenueResource::collection($revenues->get());
    }


}

