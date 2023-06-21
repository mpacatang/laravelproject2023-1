<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictProductReviewResource;
use App\Models\ProductOption;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;


class OrderProductReviewController extends Controller
{
//
    public function index(Request $request, $id): AnonymousResourceCollection
    {
        $product_reviews = ProductReview::where('customer_id', '=', $this->getUserId())
            ->where('order_id', '=', $id)
            ->get();
        return StrictProductReviewResource::collection($product_reviews);
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request, $id): StrictProductReviewResource
    {
        $request->merge(['order_id' => $id]);
        $validated_data = $this->validate($request, ProductReview::rules());

        $product_option = ProductOption::with(['product'])->findOrFail($validated_data['product_option_id']);

        $product = $product_option->product;

        $ratings_total = $product->ratings_total;
        $ratings_count = $product->ratings_count;

        $product_review = ProductReview::where('product_option_id', $product_option->id)
            ->where('customer_id', $this->getUserId())
            ->where('order_id', $id)->first();

        if ($product_review) {
            $product->ratings_total = $ratings_total + $validated_data['rating'] - $product_review->rating;
        } else {
            $product_review = new ProductReview();
            $product->ratings_total = $ratings_total + $validated_data['rating'];
            $product->ratings_count = $ratings_count + 1;
        }
        $product->rating = $product->ratings_total / $product->ratings_count;
        $validated_data = [...$validated_data,  'product_id' => $product->id, 'shop_id' => $product->shop_id];

        $product_review->fill($validated_data);

        $product_review->save();
        $product->save();

        return new StrictProductReviewResource($product_review);
    }


}

