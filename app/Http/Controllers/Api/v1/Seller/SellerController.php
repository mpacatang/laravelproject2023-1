<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\SellerResource;
use App\Http\Resources\Strict\StrictSellerResource;
use App\Models\Seller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SellerController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return SellerResource::collection(Seller::withAll()->where('shop_id', $this->getShopId())->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Seller::rules());
        $validated_data['password'] = Hash::make($validated_data['password']);
        $seller = new Seller($validated_data);
        $seller->saveAvatarImage($request);
        $seller->save();
        return $this->createdResponse('Sellers is created');
    }


    public function show(Request $request, $id): StrictSellerResource
    {
        return new StrictSellerResource(Seller::withAll()->where('shop_id', $this->getShopId())->findOrFail($id));
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_SELLER_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('seller');
        }

        $seller = Seller::where('shop_id', $this->getShopId())->findOrFail($id);
        $validated_data = $this->validate($request, Seller::rules($id));
        $seller->fill($validated_data);
        if ($request['password'] != null) {
            $seller->password = Hash::make($validated_data['password']);
        }
        $seller->saveAvatarImage($request);
        $seller->save();
        return $this->successResponse('Sellers is updated');
    }


    /**
     */
    public function removeAvatar(Request $request, $id): Response|Application|ResponseFactory
    {
        $seller = Seller::where('shop_id', $this->getShopId())->findOrFail($id);
        $seller->removeAvatar();
        $seller->save();
        return $this->successResponse('Avatar removed');
    }


    public function available(Request $request): AnonymousResourceCollection
    {
        return StrictSellerResource::collection(
            Seller::withAll()
                ->where('shop_id', '=', null)->get()
        );
    }

}

