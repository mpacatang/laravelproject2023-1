<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ModuleController extends Controller
{

    public function index(): AnonymousResourceCollection
    {
        return ModuleResource::collection(Module::all());
    }

    public function my(Request $request): ModuleResource
    {
        $shop = Shop::with('module')->findOrFail($this->getShopId());
        return new ModuleResource($shop->module);
    }
}

