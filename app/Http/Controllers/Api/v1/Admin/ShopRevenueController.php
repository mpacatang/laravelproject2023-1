<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopRevenueResource;
use App\Models\ShopRevenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ShopRevenueController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $shop_id = $request->query('shop_id');
        $till_date = $request->query('till_date');
        $module_id = $request->get('module_id');

        $shop_revenues = ShopRevenue::with(['shop']);
        if (isset($shop_id)) {
            $shop_revenues = $shop_revenues->where('shop_id', $shop_id);
        }
        if($module_id){
            $shop_revenues = $shop_revenues->whereHas('shop', function ($q) use ($module_id,) {
                $q->where('module_id', $module_id);
            });
        }
        if (isset($till_date)) {
            $shop_revenues = $shop_revenues->whereDate('created_at', '<=', Carbon::parse($till_date));
        }
        return StrictShopRevenueResource::collection($shop_revenues->get());
    }


}

