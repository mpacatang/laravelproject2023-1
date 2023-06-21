<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryBoyPayoutResource;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyPayout;
use App\Models\DeliveryBoyRevenue;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class DeliveryBoyPayoutController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictDeliveryBoyPayoutResource::collection(DeliveryBoyPayout::with('deliveryBoy')->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory|string
    {
        $validated_data = $this->validate($request, DeliveryBoyPayout::rules());
        $delivery_boy_id = $validated_data['delivery_boy_id'];
        DeliveryBoy::where('shop_id', $this->getShopId())->findOrFail($delivery_boy_id);


        $delivery_boy_revenues = DeliveryBoyRevenue::with(['deliveryBoy'])
            ->where('delivery_boy_id', $delivery_boy_id)
            ->where('created_at', '<=', Carbon::parse($validated_data['till_date']))
            ->where('delivery_boy_payout_id', '=', null)
            ->get();

        if (sizeof($delivery_boy_revenues) == 0) {
            return $this->validationErrorResponse(['amount' => 'You already paid all payments']);
        }

        $amount = 0;


        foreach ($delivery_boy_revenues as $delivery_boy_revenue) {
            $amount += $delivery_boy_revenue->take_from_shop;
            $amount -= $delivery_boy_revenue->pay_to_shop;
        }

        $abs_amount = abs($amount);
        if ($abs_amount != $validated_data['amount']) {
            return $this->validationErrorResponse(['amount' => 'It is not match with actual amount ' . $abs_amount]);
        }


        $delivery_boy_payout = new DeliveryBoyPayout();
        $delivery_boy_payout->delivery_boy_id = $delivery_boy_id;
        if ($amount > 0) {
            $delivery_boy_payout->take_from_shop = $amount;
        } else {
            $delivery_boy_payout->pay_to_shop = -$amount;
        }
        $delivery_boy_payout->till_date = Carbon::parse($validated_data['till_date']);

        DB::transaction(function () use ($delivery_boy_payout, $delivery_boy_revenues) {
            $delivery_boy_payout->save();
            foreach ($delivery_boy_revenues as $delivery_boy_revenue) {
                $delivery_boy_revenue->delivery_boy_payout_id = $delivery_boy_payout->id;
                $delivery_boy_revenue->save();
            }
        });


        return $this->createdResponse('Payout is created');
    }

}

