<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\Util\StringUtil;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class CustomerController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $customers = Customer::all();
        return StrictCustomerResource::collection($customers);
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): StrictCustomerResource
    {

        $validated_data = $this->validate($request, [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:customers,email'],
            'mobile_number' => ['required','unique:customers,mobile_number'],
        ]);


        $customer = new Customer($validated_data);
        $customer->password = Hash::make(StringUtil::generateRandomString());
        $customer->save();

        return new StrictCustomerResource($customer);

    }
}

