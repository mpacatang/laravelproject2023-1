<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryReviewResource;
use App\Models\DeliveryBoyReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class DeliveryBoyReviewController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $deliveryBoyReviews = DeliveryBoyReview::with(['deliveryBoy', 'customer'])->whereHas(
            'deliveryBoy',
            function ($query) use ($request) {
                $query->where('shop_id', $this->getShopId());
            }
        )->get();
        return StrictDeliveryReviewResource::collection($deliveryBoyReviews);
    }


}

