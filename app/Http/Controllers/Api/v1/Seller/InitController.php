<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictBusinessSettingResource;
use App\Http\Resources\Strict\StrictModuleResource;
use App\Http\Resources\Strict\StrictSellerResource;
use App\Http\Resources\Strict\StrictShopResource;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;


class InitController extends Controller
{

    public function index(Request $request): array
    {
        $data = [];
        $user = $this->user();
        if ($this->getUserId() !== null) {
            $user->load(['role', 'shop', 'shop.module']);
            if ($user->shop != null) {
                $data['shop'] = new StrictShopResource($user->shop);
                if ($user->shop->module != null) {
                    $data['module'] = new StrictModuleResource($user->shop->module);
                }
            }
            $data['seller'] = new StrictSellerResource($this->user());
        }

        $business_setup = BusinessSetting::getInstance();
        $data['business_setting'] = new StrictBusinessSettingResource($business_setup);

        return $data;
    }
}

