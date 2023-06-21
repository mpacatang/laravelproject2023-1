<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopPlanResource;
use App\Models\Shop;
use App\Models\ShopPlan;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class ShopPlanController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictShopPlanResource::collection(ShopPlan::all());
    }


    /**
     * @throws ValidationException
     */
    public function upgrade(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, [
            'shop_id' => ['required'],
            'shop_plan_id' => ['required'],
        ]);
        $shop = Shop::findOrFail($this->getShopId());
        $shop->upgradePlan($validated_data['shop_plan_id']);
        return $this->successResponse();
    }


}

