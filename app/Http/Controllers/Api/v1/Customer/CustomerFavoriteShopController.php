<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopResource;
use App\Models\CustomerFavoriteShop;
use App\Models\Shop;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class CustomerFavoriteShopController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->query('module_id');
        $user_id = $this->getUserId();
        $shopQuery = Shop::withAll();
        if (isset($module_id)) {
            $shopQuery = $shopQuery->where('module_id', $module_id);
        }
        $shopQuery->with(Shop::getFavoriteRelation($user_id))
            ->whereHas('customerFavoriteProducts', function ($q) use ($user_id, $request) {
                $q->where('customer_id', '=', $user_id);
            });

        return StrictShopResource::collection($shopQuery->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Application|ResponseFactory|Response
    {
        $validated_data = $this->validate($request, [
            'shop_id' => ['required']
        ]);
        $favorite = CustomerFavoriteShop::where('shop_id', $validated_data['shop_id'])->where(
            'customer_id',
            $this->getUserId()
        )->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            CustomerFavoriteShop::create([
                'shop_id' => $validated_data['shop_id'],
                'customer_id' => $this->getUserId()
            ]);
        }
        return $this->successResponse();
    }


}

