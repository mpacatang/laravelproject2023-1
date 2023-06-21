<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Helpers\FirebaseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryBoyResource;
use App\Models\DeliveryBoy;
use App\Models\OtpVerification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{


    /**
     * @throws ValidationException
     */
    public function register(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, DeliveryBoy::rules());
        $validated_data['password'] = Hash::make($validated_data['password']);
        $delivery_boy = new DeliveryBoy($validated_data);

        $delivery_boy->saveAvatarImage($request);
        $delivery_boy->saveIdentityImage($request);
        $delivery_boy->save();
        return $this->successResponse('Delivery is created');
    }


    /**
     * @throws ValidationException
     */
    public function login(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, DeliveryBoy::loginRules(), DeliveryBoy::loginRuleMessage());
        $delivery_boy = DeliveryBoy::getFromMobileOrEmail($request->get('mobile_number'), $request->get('email'));

        if ($delivery_boy) {
            if (Hash::check($validated_data['password'], $delivery_boy->password)) {
                $token = $delivery_boy->createToken($request->get('fcm_token'));
                return $this->successResponse(
                    ['delivery_boy' => new StrictDeliveryBoyResource($delivery_boy), 'token' => $token->access_token]
                );
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
        $validated_data = $this->validate($request, DeliveryBoy::googleLoginRules());

        $google_user = FirebaseHelper::getUserFromUID($validated_data['uid']);
        if ($google_user == null) {
            return $this->errorResponse('THis google user is not found');
        }
        $email = $google_user->email;

        $user = DeliveryBoy::where('email', $email)->first();
        if ($user == null) {
            return $this->validationErrorResponse(['email' => 'This email is not found']);
        }

        $token = $user->createToken($request->get('fcm_token'));

        return $this->successResponse(
            ['delivery_boy' => new StrictDeliveryBoyResource($user), 'token' => $token->access_token]
        );
    }


    /**
     * @throws ValidationException
     */
    public function loginViaOtp(Request $request): Response|Application|ResponseFactory
    {
        $this->validate($request, DeliveryBoy::verifyOTPRule(),);
        $otp = $request->get('otp');
        $delivery_boy = DeliveryBoy::getFromMobileOrEmail($request->get('mobile_number'), $request->get('email'));
        if ($delivery_boy) {
            $otp_verification = OtpVerification::getOTPVerificationByOTP(DeliveryBoy::class, $delivery_boy->id, $otp);
            if ($otp_verification != null) {
                $otp_verification->delete();
                $token = $delivery_boy->createToken($request->get('fcm_token'));
                return $this->successResponse(
                    ['delivery_boy' => new StrictDeliveryBoyResource($delivery_boy), 'token' => $token->access_token]
                );
            } else {
                return $this->validationErrorResponse(['otp' => 'OTP is not correct']);
            }
        } else {
            return $this->validationErrorResponse(
                ['email' => 'This email is not found', 'mobile_number' => 'This mobile number is not found']
            );
        }
    }

    /**
     * @throws ValidationException
     */
    public function sendOtp(Request $request): Response|Application|ResponseFactory
    {
        $this->validate($request, DeliveryBoy::sendOTPRule());
        $email = $request->get('email');
        $mobile_number = $request->get('mobile_number');
        $delivery_boy = DeliveryBoy::getFromMobileOrEmail($mobile_number, $email);
        if ($delivery_boy) {
            if ($email) {
                $otp_verification = OtpVerification::createOTPTokenByEmail(
                    DeliveryBoy::class,
                    $delivery_boy->id,
                    $email
                );
                if ($otp_verification->sendViaEmail()) {
                    return $this->successResponse('OTP was sent on registered email');
                }
            } else {
                $otp_verification = OtpVerification::createOTPTokenByMobile(
                    DeliveryBoy::class,
                    $delivery_boy->id,
                    $mobile_number
                );
                if ($otp_verification->sendViaSMS()) {
                    return $this->successResponse('OTP was sent on registered mobile number');
                }
            }
        } else {
            return $this->validationErrorResponse(
                ['email' => 'This email is not found', 'mobile_number' => 'This mobile number is not found']
            );
        }
        return $this->errorResponse();
    }

    public function logout(Request $request): Response|Application|ResponseFactory
    {
        if ($request->get('fcm_token') !== null) {
            $request->user()->deleteToken($request->get('fcm_token'));
        }
        return $this->successResponse();
    }
}

