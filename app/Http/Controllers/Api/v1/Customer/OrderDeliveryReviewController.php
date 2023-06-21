<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryReviewResource;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;


class OrderDeliveryReviewController extends Controller
{

    public function index(Request $request, $id): AnonymousResourceCollection
    {
        $delivery_boy_reviews = DeliveryBoyReview::where('customer_id', $this->getUserId())
            ->where('order_id', '=', $id)
            ->get();
        return StrictDeliveryReviewResource::collection($delivery_boy_reviews);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request, $id): StrictDeliveryReviewResource
    {
        $request->merge(['order_id' => $id]);

        $validated_data = $this->validate($request, DeliveryBoyReview::rules());

        $delivery_boy = DeliveryBoy::findOrFail($validated_data['delivery_boy_id']);


        $ratings_total = $delivery_boy->ratings_total;
        $ratings_count = $delivery_boy->ratings_count;

        $delivery_boy_review = DeliveryBoyReview::where('delivery_boy_id', $delivery_boy->id,)
            ->where('customer_id', $this->getUserId())
            ->where('order_id', $id)->first();

        if ($delivery_boy_review) {
            $delivery_boy->ratings_total = $ratings_total + $validated_data['rating'] - $delivery_boy->rating;
        } else {
            $delivery_boy_review = new DeliveryBoyReview();
            $delivery_boy->ratings_total = $ratings_total + $validated_data['rating'];
            $delivery_boy->ratings_count = $ratings_count + 1;
        }

        $delivery_boy_review->fill($validated_data);
        $delivery_boy_review->save();
        $delivery_boy->save();

        return new StrictDeliveryReviewResource($delivery_boy_review);
    }


}

