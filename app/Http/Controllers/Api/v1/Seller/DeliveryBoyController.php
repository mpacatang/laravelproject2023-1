<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryBoyResource;
use App\Http\Resources\Strict\StrictDeliveryBoyResource;
use App\Models\DeliveryBoy;
use App\Models\Shop;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class DeliveryBoyController extends Controller
{

    public function index(Request $request): Application|ResponseFactory|Response|AnonymousResourceCollection
    {
        $shop_id = $this->getShopId();
        $shop = Shop::findOrFail($shop_id);
        if (!$shop->self_delivery) {
            return CResponse::selfDeliveryNotActive();
        }

        return DeliveryBoyResource::collection(DeliveryBoy::with(['shop'])->where('shop_id', $shop_id)->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Application|ResponseFactory|Response|StrictDeliveryBoyResource
    {
        $shop_id = $this->getShopId();
        $shop = Shop::findOrFail($shop_id);
        if (!$shop->self_delivery) {
            return CResponse::selfDeliveryNotActive();
        }

        $validated_data = $this->validate($request, DeliveryBoy::rules());
        $validated_data['password'] = Hash::make($validated_data['password']);
        $delivery_boy = new DeliveryBoy($validated_data);

        $delivery_boy->saveAvatarImage($request);
        $delivery_boy->saveIdentityImage($request);
        $delivery_boy->save();
        return new StrictDeliveryBoyResource($delivery_boy);
    }

    public function show(Request $request, $id): Application|ResponseFactory|Response|StrictDeliveryBoyResource
    {
        $shop_id = $this->getShopId();
        $shop = Shop::findOrFail($shop_id);
        if (!$shop->self_delivery) {
            return CResponse::selfDeliveryNotActive();
        }

        return new StrictDeliveryBoyResource(
            DeliveryBoy::with(['shop'])
                ->where('shop_id', '=', $shop_id)->findOrFail($id)
        );
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Application|ResponseFactory|Response|StrictDeliveryBoyResource
    {
        if (env('DEMO', false) && env('DEMO_MAX_DELIVERY_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('delivery');
        }
        $shop_id = $this->getShopId();
        $shop = Shop::findOrFail($shop_id);
        if (!$shop->self_delivery) {
            return CResponse::selfDeliveryNotActive();
        }

        $delivery_boy = DeliveryBoy::where('shop_id', $shop_id)->findOrFail($id);
        $validated_data = $this->validate($request, DeliveryBoy::rules($id));

        if ($request->get('password')) {
            $validated_data['password'] = Hash::make($request->get('password'));
        }
        $delivery_boy->saveAvatarImage($request,);
        $delivery_boy->saveIdentityImage($request,);

        $delivery_boy->update($validated_data);
        return new DeliveryBoyResource($delivery_boy);
    }


    public function removeAvatar(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop_id = $this->getShopId();
        $shop = Shop::findOrFail($shop_id);
        if (!$shop->self_delivery) {
            return CResponse::selfDeliveryNotActive();
        }

        $delivery_boy = DeliveryBoy::where('shop_id', $shop_id)->findOrFail($id);
        $delivery_boy->removeAvatar();
        $delivery_boy->save();
        return $this->successResponse('Avatar is deleted');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response|Application|ResponseFactory
     */
    public function removeIdentityImage(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop_id = $this->getShopId();
        $shop = Shop::findOrFail($shop_id);
        if (!$shop->self_delivery) {
            return CResponse::selfDeliveryNotActive();
        }

        $delivery_boy = DeliveryBoy::where('shop_id', $shop_id)->findOrFail($id);
        $delivery_boy->removeIdentityImage();
        $delivery_boy->save();
        return $this->successResponse('Identity image is deleted');
    }


}

