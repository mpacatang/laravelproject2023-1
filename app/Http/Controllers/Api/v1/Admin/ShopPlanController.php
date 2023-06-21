<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopPlanResource;
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
    public function store(Request $request): Response|Application|ResponseFactory
    {

        $validated_data = $this->validate($request, ShopPlan::rules());

        $shopPlan = new ShopPlan($validated_data);
        $shopPlan->save();
        return $this->createdResponse('Shop plan is created');
    }

    public function show(Request $request, $id): StrictShopPlanResource
    {
        return new StrictShopPlanResource(ShopPlan::findOrFail($id));
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_SHOP_PLAN_ID',0)>=$id) {
            return CResponse::demoCreateNewError('shop plan');
        }

        $shopPlan = ShopPlan::findOrFail($id);
        $data = [...$shopPlan->toArray(), ...$request->all(),];
        $validated_data = CValidator::validate($data, ShopPlan::rules($id));
        $shopPlan->fill($validated_data);
        $shopPlan->save();
        return $this->successResponse('Shop plan is updated');
    }


}

