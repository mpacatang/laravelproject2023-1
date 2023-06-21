<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryBoyResource;
use App\Http\Resources\Strict\StrictOrderResource;
use App\Models\BusinessSetting;
use App\Models\DeliveryBoy;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

use function response;


class OrderController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $orders = Order::withAll()->orderByDesc('updated_at');
        if ($module_id) {
            $orders = $orders->where('module_id', $module_id);
        }

        return StrictOrderResource::collection($orders->get());
    }

    public function show(Request $request, $id): StrictOrderResource
    {
        return new StrictOrderResource(Order::withAll()->findOrFail($id));
    }

    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        return Response('Order is updated');
    }


    ///======================= Order Action Controller ==========================================//

    public function cancel(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->findOrFail($id);
        if ($order->canCancel()) {
            $order->cancelByShop();
        } else {
            return $this->errorResponse('You can\'t cancel order');
        }
        return new StrictOrderResource($order);
    }

    public function reject(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->findOrFail($id);
        if ($order->canReject()) {
            $order->rejectOrder($request->get('description'));
        } else {
            return $this->errorResponse('You can\'t reject order');
        }
        return new StrictOrderResource($order);
    }

    public function accept(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->findOrFail($id);
        if ($order->canAccept()) {
            if ($order->hasEnoughStock()) {
                $order->accept();
            } else {
                return $this->errorResponse('Shop haven\'t enough quantity to fulfill this order');
            }
        } else {
            return $this->errorResponse('You can\'t accept this order');
        }
        return new StrictOrderResource($order);
    }

    public function setReadyAt(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $ready_at = $request->get('ready_at');
        if (isset($ready_at)) {
            $date = Carbon::parse($ready_at);
            if ($date->gte(Carbon::now())) {
                $order = Order::withAll()->findOrFail($id);
                $order->setReadyAt($date);
                return new StrictOrderResource($order);
            } else {
                return $this->validationErrorResponse(['est_time' => ['Please set it any future date']]);
            }
        }
        return $this->validationErrorResponse(['est_time' => ['Please set estimation time']]);
    }


    public function ready(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->findOrFail($id);
        if ($order->canReady()) {
            $order->ready();
        } else {
            return $this->errorResponse('Order can\'t be ready');
        }
        return new StrictOrderResource($order);
    }

    public function assignDeliveryBoy(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $validated_data = $request->validate(['delivery_boy_id' => ['required']]);
        $delivery_boy_id = $validated_data['delivery_boy_id'];
        $order = Order::with('shop')->findOrFail($id);
        $shop = $order->shop;

        if ($shop->self_delivery) {
            $delivery_boy = DeliveryBoy::where('shop_id', $shop->id)->findOrFail($delivery_boy_id);
        } else {
            $delivery_boy = DeliveryBoy::where('id', '!=', $order->assign_delivery_boy_id)->where(
                'shop_id',
                null
            )->findOrFail($delivery_boy_id);
        }

        if (!$delivery_boy->active_for_delivery) {
            return $this->errorResponse('Delivery boy is not active');
        }

        $max_orders = BusinessSetting::maxOrderAssignToDeliveryBoy();
        $delivery_boy_orders = Order::where('assign_delivery_boy_id', $delivery_boy_id)->where(
            'complete',
            false
        )->count();

        if ($delivery_boy_orders >= $max_orders) {
            return $this->errorResponse('Delivery boy has a already maximum numbers of order');
        }

        $order->setAssignDeliveryBoy($delivery_boy_id);

        $order->loadAll();
        return new StrictOrderResource($order);
    }


    public function deliver(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->findOrFail($id);
        if ($order->canDeliver()) {
            $order->deliverByShop();
        } else {
            return $this->errorResponse('Order can\'t be deliver');
        }
        return new StrictOrderResource($order);
    }


    ///=================== Order Functionalities ==========================//


    public function setAsPaid(Request $request, $id): Response|AnonymousResourceCollection|Application|ResponseFactory
    {
        $order = Order::where('shop_id', $request->shop_id)->findOrFail($id);
        $order->setAsPaid();
        return $this->successResponse();
    }


    public function getDeliveryBoys(Request $request, $id): Response|AnonymousResourceCollection|Application|ResponseFactory {
        $order = Order::with('shop')->findOrFail($id);
        if ($order->delivery_boy_id != null) {
            return $this->errorResponse('Delivery boy already accept your order');
        }
        $shop = $order->shop;

        if ($shop->self_delivery) {
            $delivery_boys = DeliveryBoy::with('shop')->where('active_for_delivery', true)->where(
                'shop_id',
                $shop->id
            )->where('id', '!=', $order->assign_delivery_boy_id)->get();
        } else {
            $delivery_boys = DeliveryBoy::with('shop')->where('active_for_delivery', true)->where(
                'id',
                '!=',
                $order->assign_delivery_boy_id
            )->where('shop_id', null)->get();
        }


        return StrictDeliveryBoyResource::collection($delivery_boys);
    }
}

