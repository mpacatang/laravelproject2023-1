<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCartResource;
use App\Models\Addon;
use App\Models\Cart;
use App\Models\CartAddon;
use App\Models\ProductOption;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;


class CartController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $carts = Cart::withAll()
            ->where('customer_id', '=', $this->getUserId())->get();
        return StrictCartResource::collection($carts);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Application|ResponseFactory|Response|StrictCartResource
    {
        $validated_data = $this->validate($request, Cart::rules());
        $product_option = ProductOption::with(['product', 'product.module'])->findOrFail(
            $validated_data['product_option_id']
        );

        $validated_data['shop_id'] = $product_option->product->shop_id;
        $validated_data['product_id'] = $product_option->product_id;

        $cart = Cart::where('product_option_id', $product_option->id)->where('customer_id', $request->userId)->first();


        if (!isset($cart)) {
            if ($product_option->product->module->canStockEditable() && $product_option->stock == 0) {
                return $this->errorResponse("Product hasn't enough stock");
            }

            $cart = new Cart($validated_data);
            DB::transaction(function () use ($cart) {
                $cart->save();
                if (isset($item['addons'])) {
                    $c_addons = $item['addons'];
                    foreach ($c_addons as $c_addon) {
                        $addon_id = $c_addon['addon_id'];
                        $a_quantity = $c_addon['quantity'];
                        Addon::findOrFail($addon_id);
                        $cart_addon = new CartAddon([
                            'cart_id' => $cart->id,
                            'quantity' => $a_quantity,
                            'addon_id' => $addon_id
                        ]);
                        $cart_addon->save();
                    }
                }
            });
        }
        $cart->loadAll();
        return new StrictCartResource($cart);
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Application|ResponseFactory|Response|StrictCartResource
    {
        $cart = Cart::withAll()
            ->where('customer_id', '=', $this->getUserId())->findOrFail($id);
        $validated_data = $this->validate($request, Cart::updateQuantityRules());

        $quantity = $validated_data['quantity'];
        if ($cart->productOption->stock < $quantity) {
            return $this->errorResponse("Product hasn't enough stock");
        }

        $cart->quantity = $quantity;

        $cart->save();
        return new StrictCartResource($cart);
    }

    public function destroy(Request $request, $id): Response|Application|ResponseFactory
    {
        Cart::where('customer_id', $this->getUserId())->findOrFail($id)->delete();
        return $this->noContentResponse();
    }


}

