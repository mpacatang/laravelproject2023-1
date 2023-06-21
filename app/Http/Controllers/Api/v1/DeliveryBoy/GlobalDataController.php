<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictBusinessSettingResource;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;


class GlobalDataController extends Controller
{

    public function index(Request $request): StrictBusinessSettingResource
    {
        $business_setup = BusinessSetting::getInstance();
        return new StrictBusinessSettingResource($business_setup);
    }
}

