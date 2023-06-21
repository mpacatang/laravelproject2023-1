<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictProductReviewResource;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ProductReviewController extends Controller
{

    public function index(Request $request, $id): AnonymousResourceCollection
    {
        $product_reviews = ProductReview::with(['customer'])->where('product_id', '=', $id)->latest()->get();
        return StrictProductReviewResource::collection($product_reviews);
    }


}

