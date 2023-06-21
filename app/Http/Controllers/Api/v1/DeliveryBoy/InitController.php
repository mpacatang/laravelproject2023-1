<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictBusinessSettingResource;
use App\Http\Resources\Strict\StrictDeliveryBoyResource;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;


class InitController extends Controller
{

    public function index(Request $request): array
    {
        $business_setup = BusinessSetting::getInstance();
        $data['business_setting'] = new StrictBusinessSettingResource($business_setup);
        if (isset($request->user_id)) {
            $data['delivery_boy'] = new StrictDeliveryBoyResource($request->user());
        }
        return $data;
    }
}

