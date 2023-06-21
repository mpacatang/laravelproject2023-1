<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictHomeLayoutResource;
use App\Models\HomeBanner;
use App\Models\HomeLayout;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class HomeLayoutController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $home_layouts = HomeLayout::where('active', true)->get();
        $userId = $this->getUserId();
        $module_id = $request->query('module_id');
        $category_ids = $request->query('category_ids');
        if (isset($category_ids)) {
            $category_ids = explode(',', $category_ids);
        }

        $f_layouts = [];

        foreach ($home_layouts as $home_layout) {
            if ($home_layout->item_ids != null) {
                $ids = json_decode(str($home_layout->item_ids), true);
                if (HomeLayout::isShopType($home_layout->type)) {
                    $shops = Shop::with('timings')->whereIn('id', $ids);
                    if (isset($module_id)) {
                        $shops = $shops->where('module_id', $module_id);
                    }
                    $home_layout->items = $shops->get();
                    $f_layouts[] = $home_layout;
                } elseif (HomeLayout::isFeaturedProductType($home_layout->type)) {
                    $products = Product::withAll()
                        ->with(Product::getFavoriteRelation($userId))
                        ->whereIn('id', $ids);

                    $products->getQuery()->orders = null;
                    if (isset($module_id)) {
                        $products = $products->where('module_id', $module_id);
                    }
                    $home_layout->items = $products->get();
                    $f_layouts[] = $home_layout;
                } elseif (HomeLayout::isBannerType($home_layout->type)) {
                    $home_layout->items = HomeBanner::with(['shop', 'product'])->whereIn('id', $ids)->get();
                    $f_layouts[] = $home_layout;
                }
            } elseif (HomeLayout::isLatestProductType($home_layout->type)) {
                $products = Product::withAll()->with(Product::getFavoriteRelation($userId));
                if (isset($module_id)) {
                    $products = $products->where('module_id', $module_id);
                }
                $home_layout->items = $products->latest()->take(8)->get();
                $f_layouts[] = $home_layout;
            } elseif (HomeLayout::isPopularProductType($home_layout->type)) {
                $products = Product::withAll()->with(Product::getFavoriteRelation($userId));
                if (isset($module_id)) {
                    $products = $products->where('module_id', $module_id);
                }
                $home_layout->items = $products->orderBy('selling_count', 'desc')->take(8)->get();
                $f_layouts[] = $home_layout;
            } elseif (HomeLayout::isRecommendedProductsType($home_layout->type) && isset($category_ids) && sizeof(
                    $category_ids
                ) > 0) {
                $products = Product::withAll()
                    ->with(Product::getFavoriteRelation($userId))
                    ->whereIn('category_id', $category_ids);

                if (isset($module_id)) {
                    $products = $products->where('module_id', $module_id);
                }
                $products = $products->limit(8)->get();
                if (count($products) > 0) {
                    $home_layout->items = $products;
                    $f_layouts[] = $home_layout;
                }
            }
        }
        return StrictHomeLayoutResource::collection($f_layouts);
    }
}

