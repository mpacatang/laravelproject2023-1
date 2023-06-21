<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictNotificationResource;
use App\Models\DeliveryBoy;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = Notification::where('notifiable_id', '=', $this->getUserId())
            ->where('notifiable_type', '=', DeliveryBoy::class)
            ->orderByDesc('updated_at')->get();
        return StrictNotificationResource::collection($notifications);
    }


}

