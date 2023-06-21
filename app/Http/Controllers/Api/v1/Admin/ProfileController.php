<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminResource;
use App\Models\Admin;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{

    public function show(Request $request): StrictAdminResource
    {
        $user = $request->user();
        return new StrictAdminResource($user);
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request): Application|ResponseFactory|Response|StrictAdminResource
    {
        $user = ($request->user());
        if (env('DEMO', false) && env('DEMO_MAX_ADMIN_ID', 0) >= $user->id) {
            return CResponse::demoCreateNewError('admin');
        }
        $data = [...$user->toArray(), ...$request->all(),];

        $validated_data = CValidator::validate($data, Admin::rules($user->id));
        $user->fill($validated_data);
        $user->saveAvatarImage($request);
        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();
        return new StrictAdminResource($user);
    }


    public function remove_avatar(Request $request): StrictAdminResource
    {
        $user = $request->user();
        $user->removeAvatar();
        $user->save();
        return new StrictAdminResource($user);
    }


}

