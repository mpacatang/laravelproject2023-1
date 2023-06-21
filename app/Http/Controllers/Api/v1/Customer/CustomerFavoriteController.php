<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictProductResource;
use App\Http\Resources\Strict\StrictShopResource;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;


class CustomerFavoriteController extends Controller
{

    public function index(Request $request): array
    {
        $module_id = $request->query('module_id');
        $user_id = $this->getUserId();
        $productQuery = Product::withAll();
        $shopQuery = Shop::withAll();

        if (isset($module_id)) {
            $productQuery = $productQuery->where('module_id', $module_id);
            $shopQuery = $shopQuery->where('module_id', $module_id);
        }
        $productQuery->with(Product::getFavoriteRelation($user_id))
            ->whereHas('customerFavoriteProducts', function ($q) use ($user_id, $request) {
                $q->where('customer_id', '=', $user_id);
            });

        $shopQuery->with(Shop::getFavoriteRelation($user_id))
            ->whereHas('customerFavoriteShops', function ($q) use ($user_id, $request) {
                $q->where('customer_id', '=', $user_id);
            });

        return [
            'products' => StrictProductResource::collection($productQuery->get()),
            'shops' => StrictShopResource::collection($shopQuery->get()),
        ];
    }
}

