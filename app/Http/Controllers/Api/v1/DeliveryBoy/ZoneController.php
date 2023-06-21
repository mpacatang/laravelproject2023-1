<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictZoneResource;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ZoneController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictZoneResource::collection(Zone::all());
    }
}

