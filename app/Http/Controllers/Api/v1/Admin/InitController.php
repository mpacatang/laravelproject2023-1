<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminResource;
use App\Http\Resources\Strict\StrictBusinessSettingResource;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;


class InitController extends Controller
{

    public function index(Request $request): array
    {

        $data = [];
        if (isset($request->user_id)) {
            $request->user()->load('role');
            $data['admin'] = new StrictAdminResource($request->user());
        }

        $business_setup = BusinessSetting::getInstance();
        $data['business_setting'] = new StrictBusinessSettingResource($business_setup);

        return $data;

    }
}

