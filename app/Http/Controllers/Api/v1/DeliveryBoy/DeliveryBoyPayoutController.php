<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryBoyPayoutResource;
use App\Models\DeliveryBoyPayout;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class DeliveryBoyPayoutController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictDeliveryBoyPayoutResource::collection(
            DeliveryBoyPayout::where('delivery_boy_id', $this->getUserId())->get()
        );
    }

}

