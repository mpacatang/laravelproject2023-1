<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictUnitResource;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class UnitController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictUnitResource::collection(Unit::all());
    }

}

