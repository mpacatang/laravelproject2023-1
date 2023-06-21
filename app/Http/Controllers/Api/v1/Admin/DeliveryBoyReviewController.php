<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryReviewResource;
use App\Models\DeliveryBoyReview;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class DeliveryBoyReviewController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictDeliveryReviewResource::collection(DeliveryBoyReview::with(['deliveryBoy', 'customer'])->get());
    }


    public function destroy(Request $request, $id): Application|ResponseFactory|Response
    {
        DeliveryBoyReview::findOrFail($id)->delete();
        return $this->noContentResponse();
    }


}

