<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictHomeLayoutResource;
use App\Models\HomeBanner;
use App\Models\HomeLayout;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class HomeLayoutController extends Controller
{

    public function index(): AnonymousResourceCollection
    {
        $home_layouts = HomeLayout::all();
        foreach ($home_layouts as $home_layout) {
            if ($home_layout->item_ids != null) {
                $ids = json_decode(str($home_layout->item_ids), true);
                if (HomeLayout::isShopType($home_layout->type)) {
                    $home_layout->items = Shop::whereIn('id', $ids)->get();
                } elseif (HomeLayout::isFeaturedProductType($home_layout->type)) {
                    $home_layout->items = Product::withAll()->whereIn('id', $ids)->get();
                } elseif (HomeLayout::isBannerType($home_layout->type)) {
                    $home_layout->items = HomeBanner::with(['shop', 'product'])->whereIn('id', $ids)->get();
                }
            } elseif (HomeLayout::isLatestProductType($home_layout->type)) {
                $home_layout->items = Product::withAll()->latest()->take(8)->get();
            } elseif (HomeLayout::isPopularProductType($home_layout->type)) {
                $home_layout->items = Product::withAll()->orderBy('selling_count')->take(8)->get();
            }
        }

        return StrictHomeLayoutResource::collection($home_layouts);
    }


    public function update(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }
        $home_layouts = [];
        $layouts = $request->get('layouts');
        foreach ($layouts as $index => $layout) {
            $home_layout = new HomeLayout();
            if (HomeLayout::isShopType($layout['type']) || HomeLayout::isFeaturedProductType(
                    $layout['type']
                ) || HomeLayout::isBannerType($layout['type'])) {
                $home_layout->item_ids = json_encode($layout['ids']);
            } elseif (HomeLayout::isOtherType($layout['type'])) {
                $images = $layout['images'];
                $old_home_layout = HomeLayout::where('type', HomeLayout::$other_type)->first();
                $list = [];
                if ($old_home_layout != null && $old_home_layout->images != null) {
                    array_push($list, ...json_decode($old_home_layout->images));
                }
                if (isset($images) && count($images) > 0) {
                    foreach ($images as $image) {
                        $url = HomeLayout::saveLayoutImage($image);
                        if ($url != null) {
                            $list[] = $url;
                        }
                    }
                }
                $home_layout->images = json_encode($list);
            }
            $home_layout->type = $layout['type'];
            $home_layout->active = $layout['active'];
            $home_layout->priority = $index + 1;
            $home_layouts[] = $home_layout;
        }

        HomeLayout::query()->delete();
        foreach ($home_layouts as $home_layout) {
            $home_layout->save();
        }


        return $this->successResponse('Home Layout is updated');
    }

//In development
//    public function remove_image(Request $request): Response|Application|ResponseFactory
//    {
//        $image_url = $request->get('image');
//        $urls = explode('/', $image_url);
//        $url = end($urls);
//        $home_layout = HomeLayout::where('type', HomeLayout::$other_type)->first();
//        if ($home_layout != null && $home_layout->images != null) {
//            $images = json_decode($home_layout->images);
//            $image_url = null;
//            $remaining_images = [];
//            foreach ($images as $image) {
//                if (str_contains($image, $url)) {
//                    $image_url = $image;
//                } else {
//                    $remaining_images[] = $image;
//                }
//            }
//            if ($image_url != null) {
//                HomeLayout::deleteLayoutImage($image_url);
//                $home_layout->images = json_encode($remaining_images);
//                $home_layout->save();
//            }
//        }
//
//        return CResponse::no_content();
//    }
}

