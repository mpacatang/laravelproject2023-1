<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopReviewResource;
use App\Models\Shop;
use App\Models\ShopReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;


class ShopReviewController extends Controller
{


    /**
     * @throws ValidationException
     */
    public function store(Request $request, $id): StrictShopReviewResource
    {
        $request->merge(['shop_id' => $id]);
        $validated_data = $this->validate($request, ShopReview::rules());

        $shop = Shop::findOrFail($validated_data['shop_id']);

        $ratings_total = $shop->ratings_total;
        $ratings_count = $shop->ratings_count;

        $shop_review = ShopReview::where('shop_id', $id,)->where('customer_id', $this->getUserId())->first();

        if ($shop_review) {
            $shop->ratings_total = $ratings_total + $validated_data['rating'] - $shop_review->rating;
        } else {
            $shop_review = new ShopReview();
            $shop->ratings_total = $ratings_total + $validated_data['rating'];
            $shop->ratings_count = $ratings_count + 1;
        }

        $shop_review->fill($validated_data);

        $shop_review->save();
        $shop->save();

        return new StrictShopReviewResource($shop_review);
    }


    public function me(Request $request, $id): AnonymousResourceCollection
    {
        $shop_reviews = ShopReview::where('customer_id', $this->getUserId())->where('shop_id', $id)->get();
        return StrictShopReviewResource::collection($shop_reviews);
    }


}

