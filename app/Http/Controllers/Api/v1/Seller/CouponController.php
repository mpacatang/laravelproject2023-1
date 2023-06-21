<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class CouponController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictCouponResource::collection(Coupon::active()->get());
    }

}

