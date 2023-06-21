<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Strict\StrictCartResource;
use App\Http\Resources\Strict\StrictCouponResource;
use App\Http\Resources\Strict\StrictShopResource;
use App\Http\Resources\Strict\StrictShopReviewResource;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ShopController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $shops = Shop::with(['timings'])->get();
        return StrictShopResource::collection($shops);
    }


    public function show(Request $request, $id): StrictShopResource
    {
        $shop = Shop::with(
            ['timings', 'products', 'products.productOptions', 'products.productImages', 'products.subCategory']
        )
            ->with(Shop::getFavoriteRelation($this->getUserId()))
            ->findOrFail($id);
        return new StrictShopResource($shop);
    }


    public function products(Request $request, $id): AnonymousResourceCollection
    {
        $products = Product::withAll()->with('subCategory')->where('shop_id', $id)->get();
        return ProductResource::collection($products);
    }

    public function coupons(Request $request, $id): AnonymousResourceCollection
    {
        $customer_id = $request->user()->id;
        $shop = Shop::findOrFail($id);
        $coupons = Coupon::active()->with([
            'orders' => function ($q) use ($customer_id, $id) {
                $q->where('customer_id', $customer_id);
            }
        ])->where(function ($query) use ($id) {
            $query->where('shop_id', $id)
                ->orWhere('shop_id', null);
        })->where(function ($query) use ($shop, $id) {
            $query->where('module_id', $shop->module_id)
                ->orWhere('module_id', null);
        });
        if ($shop->zone_id != null) {
            $coupons->where(function ($query) use ($shop, $id) {
                $query->where('zone_id', $shop->zone_id)
                    ->orWhere('zone_id', null);
            });
        }
        $coupons = $coupons->get();

        $filtered = [];
        foreach ($coupons as $coupon) {
            if ($coupon->verify()) {
                $filtered[] = $coupon;
            }
        }
        return StrictCouponResource::collection($filtered);
    }


    public function carts(Request $request, $id): AnonymousResourceCollection
    {
        $carts = Cart::withAll()
            ->where('shop_id', $id)->where('customer_id', '=', $this->getUserId())->get();
        return StrictCartResource::collection($carts);
    }

    public function reviews(Request $request, $id): AnonymousResourceCollection
    {
        return StrictShopReviewResource::collection(
            ShopReview::with(['shop', 'customer'])->where('shop_id', $id)->get()
        );
    }


}

