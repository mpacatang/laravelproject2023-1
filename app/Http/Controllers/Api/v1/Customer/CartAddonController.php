<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCartAddonResource;
use App\Models\Cart;
use App\Models\CartAddon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class CartAddonController extends Controller
{


    /**
     * @throws ValidationException
     */
    public function store(Request $request): StrictCartAddonResource
    {
        $validated_data = $this->validate($request, CartAddon::rules());
        $cart = Cart::where('customer_id', $this->getUserId())->findOrFail($validated_data['cart_id']);

        $cart_addon = new CartAddon($validated_data);
        $cart_addon->quantity = 1;
        $cart_addon->save();

        $cart_addon->load('addon');
        return new StrictCartAddonResource($cart_addon);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): StrictCartAddonResource
    {
        $cart_addon = CartAddon::with('addon')->whereHas(
            'cart',
            function ($q) use ($request) {
                $q->where('customer_id', $this->getUserId());
            }
        )->findOrFail($id);
        $validated_data = $this->validate($request, CartAddon::updateQuantityRules());

        $cart_addon->quantity = $validated_data['quantity'];

        $cart_addon->save();

        return new StrictCartAddonResource($cart_addon);
    }

    public function destroy(Request $request, $id): Application|ResponseFactory|Response
    {
        $cart = Cart::whereHas('addons', function ($q) use ($id) {
            $q->where('id', $id);
        })->first();
        CartAddon::whereHas(
            'cart',
            function ($q) use ($request) {
                $q->where('customer_id', $this->getUserId());
            }
        )->findOrFail($id)->delete();
        return $this->successResponse();
    }

}

