<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCouponResource;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class CouponController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictCouponResource::collection(Coupon::all());
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Coupon::rules(), Coupon::ruleMessages());
        $coupon = new Coupon($validated_data);
        if ($validated_data['first_order']) {
            $coupon->limit = 1;
        }
        $coupon->started_at = Carbon::parse($validated_data['started_at']);
        $coupon->expired_at = Carbon::parse($validated_data['expired_at']);
        $coupon->save();
        return $this->createdResponse('Coupon is created');
    }


    public function show(Request $request, $id): StrictCouponResource
    {
        return new StrictCouponResource(Coupon::findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory|StrictCouponResource
    {
        if (env('DEMO', false) && env('DEMO_MAX_COUPON_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('coupon');
        }

        $coupon = Coupon::findOrFail($id);

        $validated_data = CValidator::validate([...$coupon->toArray(), ...$request->all()],
            Coupon::rules($id),
            Coupon::ruleMessages());
        $coupon->fill($validated_data);
        if ($validated_data['first_order']) {
            $coupon->limit = 1;
        }
        if ($request->has('started_at')) {
            $coupon->started_at = Carbon::parse($request->get('started_at'));
        }
        if ($request->has('expired_at')) {
            $coupon->expired_at = Carbon::parse($request->get('expired_at'));
        }

        $coupon->save();

        return new StrictCouponResource($coupon);
    }

}

