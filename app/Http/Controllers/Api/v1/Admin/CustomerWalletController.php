<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCustomerWalletResource;
use App\Models\CustomerWallet;
use App\Models\CustomerWalletTransaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CustomerWalletController extends Controller
{

    public function index(Request $request, $id): StrictCustomerWalletResource
    {
        $wallet = CustomerWallet::with(['transactions'])->where('customer_id', $id)->firstOrFail();
        return new StrictCustomerWalletResource($wallet);
    }


    public function addMoney(Request $request, $id): Response|Application|ResponseFactory
    {
        $validated_data = $request->validate([
            'amount' => 'required'
        ]);
        CustomerWallet::addMoney($id, $validated_data['amount'], CustomerWalletTransaction::$from_admin_method);

        return $this->successResponse();
    }


}

