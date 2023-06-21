<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCustomerLoyaltyWalletResource;
use App\Models\BusinessSetting;
use App\Models\CustomerLoyaltyWallet;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class CustomerLoyaltyWalletController extends Controller
{

    public function index(Request $request): StrictCustomerLoyaltyWalletResource
    {
        $wallet = CustomerLoyaltyWallet::with(['transactions'])->where('customer_id', $request->userId)->first();
        return new StrictCustomerLoyaltyWalletResource($wallet);
    }


    /**
     * @throws ValidationException
     */
    public function convertPoints(Request $request): Application|ResponseFactory|Response
    {
        $data = $this->validate($request, [
            'point' => ['required', 'numeric']
        ]);
        $wallet = CustomerLoyaltyWallet::where('customer_id', $this->getUserId())->firstOrFail();
        $point = $data['point'];
        $minPoint = BusinessSetting::_get(BusinessSetting::$customer_minimum_loyalty_point_to_convert);
        if ($point < $minPoint || $point > $wallet->point) {
            return $this->errorResponse('You have not enough point to convert');
        }
        $wallet->convertPoint($point);
        return $this->errorResponse();
    }
}

