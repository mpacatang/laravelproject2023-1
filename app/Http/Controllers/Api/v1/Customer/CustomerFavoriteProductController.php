<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictProductResource;
use App\Models\CustomerFavoriteProduct;
use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class CustomerFavoriteProductController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->query('module_id');
        $user_id = $this->getUserId();
        $productQuery = Product::withAll();
        if (isset($module_id)) {
            $productQuery = $productQuery->where('module_id', $module_id);
        }
        $productQuery->with(Product::getFavoriteRelation($user_id))
            ->whereHas('customerFavoriteProducts', function ($q) use ($user_id, $request) {
                $q->where('customer_id', '=', $user_id);
            });
        return StrictProductResource::collection($productQuery->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Application|ResponseFactory|Response
    {
        $validated_data = $this->validate($request, [
            'product_id' => ['required']
        ]);
        $favorite = CustomerFavoriteProduct::where('product_id', $validated_data['product_id'])
            ->where('customer_id', $this->getUserId())->first();
        if ($favorite) {
            $favorite->delete();
        } else {
            CustomerFavoriteProduct::create([
                'product_id' => $validated_data['product_id'],
                'customer_id' => $this->getUserId()
            ]);
        }
        return $this->successResponse();
    }


}

