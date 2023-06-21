<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCustomerAddressResource;
use App\Models\Cart;
use App\Models\CustomerAddress;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class CustomerAddressController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $customer_addresses = CustomerAddress::where('customer_id', '=', $this->getUserId())->where('active', true)->get();
        return StrictCustomerAddressResource::collection($customer_addresses);
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): StrictCustomerAddressResource
    {
        $validated_data = $this->validate($request, CustomerAddress::rules(), CustomerAddress::ruleMessages());
        $customer_address = new CustomerAddress($validated_data);
        $customer_address->save();
        return new StrictCustomerAddressResource($customer_address);

    }

    public function destroy(Request $request, $id): Response|Application|ResponseFactory
    {
        $cart = Cart::where('customer_id',  $this->getUserId())->findOrFail($id);
        $cart->delete();
        return $this->successResponse();
    }


    public function selected(Request $request, $id): StrictCustomerAddressResource
    {
        $customer_address = CustomerAddress::where('customer_id', '=', $this->getUserId())->findOrFail($id);
        CustomerAddress::where('customer_id', '=', $this->getUserId())->where('selected', true)->update(['selected' => false]);
        $customer_address->selected = true;
        $customer_address->save();
        return new StrictCustomerAddressResource($customer_address);
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): StrictCustomerAddressResource
    {
        $customer_address = CustomerAddress::where('customer_id', '=', $this->getUserId())->findOrFail($id);
        $validated_data = $this->validate($request, CustomerAddress::rules($id), CustomerAddress::ruleMessages());
        $customer_address->update($validated_data);
        $customer_address->save();
        return new StrictCustomerAddressResource($customer_address);
    }


}

