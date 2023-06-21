<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopReviewResource;
use App\Models\ShopReview;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class ShopReviewController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $reviews = ShopReview::with(['shop', 'customer']);
        if ($module_id) {
            $reviews = $reviews->whereHas('shop', function ($q) use ($module_id,) {
                $q->where('module_id', $module_id);
            });
        }
        return StrictShopReviewResource::collection($reviews->get());
    }

    public function destroy(Request $request, $id): Application|ResponseFactory|Response
    {
        ShopReview::findOrFail($id)->delete();
        return $this->successResponse();
    }

}

