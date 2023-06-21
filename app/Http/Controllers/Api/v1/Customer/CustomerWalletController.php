<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCustomerWalletResource;
use App\Models\CustomerWallet;
use Illuminate\Http\Request;


class CustomerWalletController extends Controller
{

    public function index(Request $request): StrictCustomerWalletResource
    {
        $wallet = CustomerWallet::with(['transactions'])->where('customer_id', $this->getUserId())->first();
        return new StrictCustomerWalletResource($wallet);
    }
}

