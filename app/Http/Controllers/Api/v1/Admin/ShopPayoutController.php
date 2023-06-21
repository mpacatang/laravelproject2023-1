<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopPayoutResource;
use App\Models\ShopPayout;
use App\Models\ShopRevenue;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class ShopPayoutController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $payouts = ShopPayout::with('shop');
        if ($module_id) {
            $payouts = $payouts->whereHas('shop', function ($q) use ($module_id,) {
                $q->where('module_id', $module_id);
            });
        }
        return StrictShopPayoutResource::collection($payouts->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, ShopPayout::rules());
        $shop_id = $validated_data['shop_id'];
        $shop_revenues = ShopRevenue::with(['shop'])
            ->where('shop_id', $shop_id)
            ->where('created_at', '<=', Carbon::parse($validated_data['till_date']))
            ->where('shop_payout_id', '=', null)
            ->get();

        if (sizeof($shop_revenues) == 0) {
            return $this->validationErrorResponse(['amount' => 'You already paid all payments']);
        }

        $amount = 0;

        foreach ($shop_revenues as $shop_revenue) {
            $amount += $shop_revenue->take_from_admin;
            $amount -= $shop_revenue->pay_to_admin;
        }
        $abs_amount = abs($amount);
        if ($abs_amount != $validated_data['amount']) {
            return $this->validationErrorResponse(['amount' => 'It is not match with actual amount ' . $abs_amount]);
        }

        $shop_payout = new ShopPayout();
        $shop_payout->shop_id = $shop_id;
        if ($amount > 0) {
            $shop_payout->pay_to_shop = $amount;
        } else {
            $shop_payout->take_from_shop = -$amount;
        }
        $shop_payout->till_date = Carbon::parse($validated_data['till_date']);

        DB::transaction(function () use ($shop_revenues, $shop_payout) {
            $shop_payout->save();
            foreach ($shop_revenues as $shop_revenue) {
                $shop_revenue->shop_payout_id = $shop_payout->id;
                $shop_revenue->save();
            }
        });


        return $this->successResponse('Payout is created');
    }

}

