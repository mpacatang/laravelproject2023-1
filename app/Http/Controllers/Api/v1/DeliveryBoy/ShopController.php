<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ShopController extends Controller
{


    public function show(Request $request): ShopResource|Application|ResponseFactory|Response
    {
        $shop_id = $request->user()->shop_id;
        return $shop_id ? new ShopResource(Shop::withAll()->find($shop_id)) : $this->errorResponse(
            'You haven\'t any shop yet'
        );
    }


}

