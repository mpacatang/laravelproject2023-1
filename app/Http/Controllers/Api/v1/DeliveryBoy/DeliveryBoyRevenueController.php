<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryBoyRevenueResource;
use App\Models\DeliveryBoyRevenue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class DeliveryBoyRevenueController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictDeliveryBoyRevenueResource::collection(
            DeliveryBoyRevenue::where('delivery_boy_id', $this->getUserId())->get()
        );
    }

}

