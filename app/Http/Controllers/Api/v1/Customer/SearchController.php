<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictProductResource;
use App\Http\Resources\Strict\StrictShopResource;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;


class SearchController extends Controller
{

    public function index(Request $request): array
    {
        $q = $request->query('q');
        $module_id = $request->query('module_id');
        $categories = $request->query('categories');
        if (isset($categories)) {
            $categories = explode(',', $categories);
        }
        $min_rating = $request->query('min_rating');
        $min_price = $request->query('min_price');
        $max_price = $request->query('max_price');


        $productQuery = Product::withAll()->with(Product::getFavoriteRelation($this->getUserId()))->active();
        $shopQuery = Shop::withAll()->with(Shop::getFavoriteRelation($this->getUserId()))->active();

        if (isset($module_id)) {
            $productQuery = $productQuery->where('module_id', $module_id);
            $shopQuery = $shopQuery->where('module_id', $module_id);
        }
        if (isset($q)) {
            $productQuery = $productQuery->where('name', 'like', "%" . $q . "%");
            $shopQuery = $shopQuery->where('name', 'like', "%" . $q . "%");
        }
        if (isset($categories)) {
            $productQuery = $productQuery->whereIn('category_id', $categories);
            $shopQuery->whereHas('module.categories', function ($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }
        if (isset($min_rating)) {
            $productQuery = $productQuery->where('rating', '>=', $min_rating);
            $shopQuery = $shopQuery->where('rating', '>=', $min_rating);
        }
        if (isset($max_price) | isset($min_price)) {
            $productQuery = $productQuery->whereHas('productOptions', function ($q) use ($max_price, $min_price) {
                if (isset($min_price)) {
                    $q = $q->where('calculated_price', '>=', $min_price);
                }
                if (isset($max_price)) {
                    $q->where('calculated_price', '<=', $max_price);
                }
            });
        }
        return [
            'products' => StrictProductResource::collection($productQuery->get()),
            'shops' => StrictShopResource::collection($shopQuery->get()),
        ];
    }


}

