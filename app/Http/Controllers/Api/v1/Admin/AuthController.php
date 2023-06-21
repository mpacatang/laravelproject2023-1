<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\FirebaseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminResource;
use App\Models\Admin;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function register(): int
    {
        return 0;
    }


    /**
     * @throws ValidationException
     */
    public function login(Request $request): Response|Application|ResponseFactory
    {
        $this->validate($request, Admin::loginRules());
        $admin = Admin::getFromMobileOrEmail($request->get('mobile_number'), $request->get('email'));
        if ($admin) {
            $admin->load('role');
            if (Hash::check($request->get('password'), $admin->password)) {
                $token = $admin->createToken($request->get('fcm_token'));
                return $this->successResponse(['admin' => new StrictAdminResource($admin), 'token' => $token->access_token]);
            } else {
                return $this->validationErrorResponse(['password' => 'Password is not correct']);
            }
        } else {
            return $this->validationErrorResponse(['email' => 'This email is not found']);
        }
    }


    /**
     * @throws ValidationException
     */
    public function googleLogin(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Admin::googleLoginRules());

        $google_user = FirebaseHelper::getUserFromUID($validated_data['uid']);
        if ($google_user == null) {
            return $this->errorResponse('This google user is not found');
        }
        $email = $google_user->email;

        $user = Admin::where('email', $email)->first();
        if ($user == null) {
            return $this->errorResponse('This email is not found');
        }

        $token = $user->createToken($request->get('fcm_token'));

        return $this->successResponse(['admin' => new StrictAdminResource($user), 'token' => $token->access_token]);
    }


    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory
     */
    public function logout(Request $request): Response|Application|ResponseFactory
    {
        if ($request->has('fcm_token')) {
            $request->user()->deleteToken($request->get('fcm_token'));
        }
        return $this->successResponse();
    }


}

