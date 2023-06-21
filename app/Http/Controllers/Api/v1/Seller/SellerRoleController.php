<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictSellerRoleResource;
use App\Models\SellerRole;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SellerRoleController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictSellerRoleResource::collection(SellerRole::where('shop_id', $this->getShopId())->get());
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, SellerRole::rules(), SellerRole::ruleMessages());
        $role = new SellerRole($validated_data);
        $role->permissions = json_encode($validated_data['permissions']);
        $role->save();
        return $this->successResponse('Role is created');
    }

    public function show(Request $request, $id): StrictSellerRoleResource
    {

        $role = SellerRole::where('shop_id', $this->getShopId())->findOrFail($id);
        return new StrictSellerRoleResource($role);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_SELLER_ROLE_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('seller role');
        }
        $role = SellerRole::where('shop_id', $this->getShopId())->findOrFail($id);
        $validated_data = $this->validate($request, SellerRole::rules($id));
        $role->fill($validated_data);

        $role->permissions = json_encode($validated_data['permissions']);
        $role->save();
        return $this->successResponse('Role is updated');
    }


}

