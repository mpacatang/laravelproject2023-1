<?php

namespace App\Http\Middleware;

use App\Helpers\CResponse;
use App\Models\BusinessSetting;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class EnsureCustomerVerified
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return Application|ResponseFactory|CResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();
        $can_email_verify = BusinessSetting::getCustomerEmailVerification();
        $can_mobile_verify = BusinessSetting::getCustomerMobileNumberVerification();

        $email_verified = $user->email_verified_at != null;
        $mobile_verified = $user->mobile_number_verified_at != null;

        if (($can_mobile_verify && $mobile_verified) ||
            $can_email_verify && $email_verified) {
            return $next($request);
        }

        if($can_mobile_verify){
            return $this->mobileNumberIsNotVerified();
        }
        if($can_email_verify){
            return $this->emailIsNotVerified();
        }

        return $next($request);
    }


    protected function mobileNumberIsNotVerified(): Response|Application|ResponseFactory
    {
        return Response("Mobile Number is not verified", CResponse::$MOBILE_NUMBER_NOT_VERIFIED);
    }

    protected function emailIsNotVerified(): Response|Application|ResponseFactory
    {
        return Response("Email is not verified", CResponse::$EMAIL_NOT_VERIFIED);
    }
}
