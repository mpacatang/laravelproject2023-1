<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryBoyResource;
use App\Http\Resources\Strict\StrictDeliveryBoyResource;
use App\Models\DeliveryBoy;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class DeliveryBoyController extends Controller
{

    public function index(): AnonymousResourceCollection
    {
        return StrictDeliveryBoyResource::collection(DeliveryBoy::with(['shop'])->get());
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, DeliveryBoy::rules());
        $delivery_boy = new DeliveryBoy($validated_data);
        $delivery_boy['password'] = Hash::make($validated_data['password']);
        $delivery_boy->saveAvatarImage($request);
        $delivery_boy->saveIdentityImage($request);
        $delivery_boy->save();
        return $this->createdResponse('Delivery is created');
    }

    public function show(Request $request, $id): StrictDeliveryBoyResource
    {
        return new DeliveryBoyResource(DeliveryBoy::with(['shop', 'zone'])->findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_DELIVERY_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('delivery');
        }
        $delivery_boy = DeliveryBoy::findOrFail($id);
        $validated_data = $this->validate($request, DeliveryBoy::rules($id));

        if ($request->get('password')) {
            $validated_data['password'] = Hash::make($request->get('password'));
        }
        $delivery_boy->saveAvatarImage($request);
        $delivery_boy->saveIdentityImage($request);

        $delivery_boy->update($validated_data);

        return $this->successResponse('Delivery boy updated');
    }

    public function approve(Request $request, $id): Response|Application|ResponseFactory
    {
        $delivery_boy = DeliveryBoy::findOrFail($id);
        if ($delivery_boy->approved) {
            return $this->errorResponse("This delivery is already approved");
        }
        $delivery_boy->approved = true;
        $delivery_boy->archived = false;
        $delivery_boy->save();
        return $this->successResponse('Delivery boy approved');
    }

    public function destroy(Request $request, $id): Response|Application|ResponseFactory
    {
        $delivery_boy = DeliveryBoy::findOrFail($id);
        $delivery_boy->archived = true;
        $delivery_boy->active = false;
        $delivery_boy->approved = false;
        $delivery_boy->save();
        return $this->successResponse('Shop is archived');
    }

    public function removeAvatar(Request $request, $id): Response|Application|ResponseFactory
    {
        $delivery_boy = DeliveryBoy::findOrFail($id);
        $delivery_boy->removeAvatar();
        $delivery_boy->save();
        return $this->successResponse('Avatar is deleted');
    }

    public function removeIdentityImage(Request $request, $id): Response|Application|ResponseFactory
    {
        $delivery_boy = DeliveryBoy::findOrFail($id);
        $delivery_boy->removeIdentityImage();
        $delivery_boy->save();
        return $this->successResponse('Identity image is deleted');
    }

}

