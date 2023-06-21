<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminRoleResource;
use App\Models\AdminRole;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AdminRoleController extends Controller
{

    public function index(): AnonymousResourceCollection
    {
        return StrictAdminRoleResource::collection(AdminRole::all());
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, AdminRole::rules(), AdminRole::ruleMessages());
        $role = new AdminRole($validated_data);
        $role->permissions = json_encode($validated_data['permissions']);
        $role->save();
        return $this->createdResponse('Role is created');
    }


    public function show(Request $request, $id): StrictAdminRoleResource
    {
        return new StrictAdminRoleResource(AdminRole::findOrFail($id));
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_ADMIN_ROLE_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('admin role');
        }
        $role = AdminRole::findOrFail($id);
        $validated_data = $this->validate($request, AdminRole::rules($id), AdminRole::ruleMessages());
        $role->fill($validated_data);
        $role->permissions = json_encode($validated_data['permissions']);
        $role->save();
        return $this->successResponse('Role is updated');
    }


}

