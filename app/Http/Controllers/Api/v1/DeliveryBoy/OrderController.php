<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictOrderResource;
use App\Models\BusinessSetting;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class OrderController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::withAll()
            ->where('assign_delivery_boy_id', '=', $this->getUserId())
            ->orWhere('delivery_boy_id', '=', $this->getUserId())
            ->orderByDesc('updated_at')->get();
        return StrictOrderResource::collection($orders);
    }

    public function show(Request $request, $id): StrictOrderResource
    {
        $order = Order::withAll()->where('assign_delivery_boy_id', '=', $this->getUserId())->findOrFail($id);
        return new StrictOrderResource($order);
    }

    ///======================= Order Action Controller ==========================================//

    public function reject(Request $request, $id): StrictOrderResource|Application|ResponseFactory|Response
    {
        $order = Order::where('assign_delivery_boy_id', $this->getUserId())->where('complete', false)->find($id);
        if ($order) {
            $order->rejectForDelivery();
            $order->loadAll();
            return new StrictOrderResource($order);
        } else {
            return $this->errorResponse('This order is already assign to other');
        }
    }

    public function accept(Request $request, $id): StrictOrderResource|Application|ResponseFactory|Response
    {
        $order = Order::where('assign_delivery_boy_id', $this->getUserId())->where('complete', false)->find($id);
        if ($order) {
            $order->acceptForDelivery();
            $order->loadAll();
            return new StrictOrderResource($order);
        } else {
            return $this->errorResponse('This order is already assign to other');
        }
    }

    public function pickup(Request $request, $id): StrictOrderResource
    {
        $order = Order::where('delivery_boy_id', $this->getUserId())->where('complete', false)->findOrFail($id);
        $order->pickupByDelivery();
        $order->loadAll();
        return new StrictOrderResource($order);
    }

    /**
     * @throws ValidationException
     */
    public function deliver(Request $request, $id): Response|StrictOrderResource|Application|ResponseFactory
    {
        $order = Order::where('delivery_boy_id', $this->getUserId())->where('complete', false)->findOrFail($id);
        if (BusinessSetting::isEnableDeliveryOtpVerificationForDeliveryBoy()) {
            $validated_data = $this->validate($request, Order::deliveryRules(), Order::deliveryMessages());
            if ($order->otp != $validated_data['otp']) {
                return $this->validationErrorResponse(['otp' => 'Provided otp is not correct']);
            }
        }

        $order->deliverByDeliveryBoy();

        $order->loadAll();
        return new StrictOrderResource($order);
    }


}

