<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopTimeResource;
use App\Models\Shop;
use App\Models\ShopTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class ShopTimeController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $shop_id = $request->user()->shop_id;

        $shop = Shop::with('timings')->findOrFail($shop_id);
        return StrictShopTimeResource::collection($shop->timings);
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Application|ResponseFactory|Response|StrictShopTimeResource
    {
        $shop_id = $request->get('shop_id');
        $validated_data = $this->validate($request, ShopTime::rules());
        $open_at = $validated_data['open_at'];
        $close_at = $validated_data['close_at'];
        $overlapping = ShopTime::where('day', $validated_data['day'])->where('shop_id', $shop_id)
            ->where(function ($q) use ($close_at, $open_at) {
                return $q->where(function ($query) use ($close_at, $open_at) {
                    return $query->where('open_at', '<=', $open_at)->where('close_at', '>=', $open_at);
                })->orWhere(function ($query) use ($open_at, $close_at) {
                    return $query->where('open_at', '<=', $close_at)->where('close_at', '>=', $close_at);
                });
            })
            ->first();
        if ($overlapping) {
            return $this->validationErrorResponse(['open_at' => 'This timing is overlapping with other', 'close_at' => 'This timing is overlapping with other']);
        }
        $shop_time = new ShopTime($validated_data);
        $shop_time->save();
        return new StrictShopTimeResource($shop_time);
    }

    public function destroy(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop_id = $request->get('shop_id');
        ShopTime::where('shop_id', $shop_id)->findOrFail($id)->delete();
        return $this->successResponse();
    }

}

