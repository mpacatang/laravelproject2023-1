<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopRevenueResource;
use App\Models\ShopRevenue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ShopRevenueController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictShopRevenueResource::collection(ShopRevenue::where('shop_id', $this->getShopId())->get());
    }

}

