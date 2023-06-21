<?php

namespace App\Http\Controllers\Api\v1\Admin\Notifications;

use App\Helpers\Notification\FCMOption;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictManualNotificationResource;
use App\Models\ManualNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class ManualNotificationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return StrictManualNotificationResource::collection(ManualNotification::query()->latest()->get());
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, ManualNotification::rules());

        $schedule = $request->get('schedule_at');
        $schedule_at = null;
        if (isset($schedule)) {
            $schedule_at = Carbon::parse($schedule);
        }

        $all_customers = $request->get('all_customers');
        $all_sellers = $request->get('all_sellers');
        $all_delivery_boys = $request->get('all_delivery_boys');
        $valid = $all_customers || $all_sellers || $all_delivery_boys;

        if (!$valid) {
            return $this->errorResponse("Please check any users then send");
        }

        $notification = new ManualNotification($validated_data);
        $notification['all_customers'] = $all_customers ?? false;
        $notification['all_sellers'] = $all_sellers ?? false;
        $notification['all_delivery_boys'] = $all_delivery_boys ?? false;
        $notification['schedule_at'] = $schedule_at;
        $notification['data'] = [
            'type' => FCMOption::$other_type
        ];
        $notification->saveImageFromRequest($request);
        $notification->save();

        $notification->send_notification();

        return $this->successResponse('Notification Sent');
    }

    public function show(Request $request, $id): StrictManualNotificationResource
    {
        return new StrictManualNotificationResource(ManualNotification::findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        $notification = ManualNotification::findOrFail($id);
        if($notification->schedule_at==null || $notification->schedule_at<now()){
            return $this->errorResponse('It\'s already sent. You can\'t edit');
        }

        $this->validate($request, ManualNotification::rules());

        $schedule = $request->get('schedule_at');
        $schedule_at = null;
        if (isset($schedule)) {
            $schedule_at = Carbon::parse($schedule);
        }

        $all_customers = $request->get('all_customers');
        $all_sellers = $request->get('all_sellers');
        $all_delivery_boys = $request->get('all_delivery_boys');
        $valid = $all_customers || $all_sellers || $all_delivery_boys;

        if (!$valid) {
            return $this->errorResponse("Please check any users then send");
        }

        $notification['all_customers'] = $all_customers ?? false;
        $notification['all_sellers'] = $all_sellers ?? false;
        $notification['all_delivery_boys'] = $all_delivery_boys ?? false;
        $notification['schedule_at'] = $schedule_at;
        $notification['data'] = [
            'type' => FCMOption::$other_type
        ];
        $notification->saveImageFromRequest($request);

        $notification->save();

        return $this->successResponse('Notification Updated');
    }

}

