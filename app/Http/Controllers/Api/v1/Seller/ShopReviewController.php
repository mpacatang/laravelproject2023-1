<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopReviewResource;
use App\Models\ShopReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ShopReviewController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictShopReviewResource::collection(
            ShopReview::with(['shop', 'customer'])->where('shop_id', $this->getShopId())->get());
    }


}

