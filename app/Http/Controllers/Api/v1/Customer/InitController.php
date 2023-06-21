<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictBusinessSettingResource;
use App\Http\Resources\Strict\StrictCartResource;
use App\Http\Resources\Strict\StrictCustomerAddressResource;
use App\Http\Resources\Strict\StrictCustomerResource;
use App\Models\BusinessSetting;
use App\Models\Cart;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;


class InitController extends Controller
{

    public function index(Request $request): array
    {

        $data = [];
        if (isset($request->user_id)) {
            $customer_addresses = CustomerAddress::where('customer_id', '=', $request->user_id)->where('active', true)->get();
            $data['customer_addresses'] = StrictCustomerAddressResource::collection($customer_addresses);
            $carts = Cart::withAll()->where('customer_id', '=', $request->user_id)->get();
            $data['carts'] = StrictCartResource::collection($carts);
            $data['customer'] = new StrictCustomerResource($request->user());
        }

        $business_setup = BusinessSetting::getInstance();
        $data['business_setting'] = new StrictBusinessSettingResource($business_setup);

        return $data;

    }
}

