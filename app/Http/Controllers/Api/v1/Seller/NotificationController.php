<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictNotificationResource;
use App\Models\Notification;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class NotificationController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictNotificationResource::collection(
            Notification::where('notifiable_id', '=', $this->getShopId())
                ->where('notifiable_type', '=', Seller::class)
                ->orderByDesc('updated_at')->get()
        );
    }


}

