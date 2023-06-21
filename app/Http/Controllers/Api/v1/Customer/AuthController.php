<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Helpers\FirebaseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCustomerResource;
use App\Models\Customer;
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
        $validated_data = $this->validate($request, Customer::registerRules(), Customer::registerRuleMessage());

        $customer = new Customer($validated_data);
        $customer->password = Hash::make($validated_data['password']);

        $customer->save();

        $token = $customer->createToken($request->get('fcm_token'));
        return $this->successResponse(
            ['customer' => new StrictCustomerResource($customer), 'token' => $token->access_token]
        );
    }


    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory
     * @throws ValidationException
     */
    public function googleLogin(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Customer::googleLoginRules());

        $google_user = FirebaseHelper::getUserFromUID($validated_data['uid']);
        if ($google_user == null) {
            return $this->errorResponse('This google user is not found');
        }
        $email = $google_user->email;

        $customer = Customer::where('email', $email)->first();
        if ($customer == null) {
            return $this->errorResponse('This email is not found');
        }

        $token = $customer->createToken($request->get('fcm_token'));


        return $this->successResponse(
            ['customer' => new StrictCustomerResource($customer), 'token' => $token->access_token]
        );
    }


    /**
     * @throws ValidationException
     */
    public function login(Request $request): Response|Application|ResponseFactory
    {
        $this->validate($request, Customer::loginRules(), Customer::loginRuleMessage());
        $customer = Customer::getFromMobileOrEmail($request->get('mobile_number'), $request->get('email'));

        if ($customer) {
            if (Hash::check($request->get('password'), $customer->password)) {
                $token = $customer->createToken($request->get('fcm_token'));
                return $this->successResponse(
                    ['customer' => new StrictCustomerResource($customer), 'token' => $token->access_token]
                );
            } else {
                return $this->validationErrorResponse(['password' => 'Password is not corrects']);
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
    public function loginViaOtp(Request $request): Response|Application|ResponseFactory
    {
        $this->validate($request, Customer::verifyOTPRule());
        $otp = $request->get('otp');
        $customer = Customer::getFromMobileOrEmail($request->get('mobile_number'), $request->get('email'));
        if ($customer) {
            $otp_verification = OtpVerification::getOTPVerificationByOTP(Customer::class, $customer->id, $otp);
            if ($otp_verification != null) {
                $otp_verification->delete();
                $token = $customer->createToken($request->get('fcm_token'));
                return $this->successResponse(
                    ['customer' => new StrictCustomerResource($customer), 'token' => $token->access_token]
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
        $this->validate($request, Customer::sendOTPRule());
        $email = $request->get('email');
        $mobile_number = $request->get('mobile_number');
        $customer = Customer::getFromMobileOrEmail($mobile_number, $email);
        if ($customer) {
            if ($email) {
                $otp_verification = OtpVerification::createOTPTokenByEmail(Customer::class, $customer->id, $email);
                if ($otp_verification->sendViaEmail()) {
                    return $this->successResponse('OTP was sent on registered email');
                }
            } else {
                $otp_verification = OtpVerification::createOTPTokenByMobile(
                    Customer::class,
                    $customer->id,
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


    /**
     * @throws ValidationException
     */
    public function checkMobileNumber(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, [
            'mobile_number' => ['required']
        ]);
        if (Customer::where('mobile_number', $validated_data['mobile_number'])
            ->where('id', '!=', $this->getUserId())->exists()) {
            return $this->validationErrorResponse(['mobile_number' => 'This number is already registered']);
        }
        return $this->successResponse();
    }


    public function logout(Request $request): Response|Application|ResponseFactory
    {
        if ($request->get('fcm_token') !== null) {
            $request->user()->deleteToken($request->get('fcm_token'));
        }
        return $this->successResponse();
    }

}

