<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopTimeResource;
use App\Models\ShopTime;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class ShopTimeController extends Controller
{


    /**
     * @throws ValidationException
     */
    public function store(Request $request, $shopId): Application|ResponseFactory|Response|StrictShopTimeResource
    {
        $shop_id = $shopId;
        $request->merge(['shop_id' => $shop_id]);
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
            return $this->validationErrorResponse(
                [
                    'open_at' => 'This timing is overlapping with other',
                    'close_at' => 'This timing is overlapping with other'
                ]
            );
        }
        $shop_time = new ShopTime($validated_data);
        $shop_time->save();
        return new StrictShopTimeResource($shop_time);
    }

    public function destroy(Request $request, $shopId, $id): Response|Application|ResponseFactory
    {
        ShopTime::where('shop_id', $shopId)->findOrFail($id)->delete();
        return $this->successResponse();
    }

}

