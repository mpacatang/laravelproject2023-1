<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\BusinessHelper;
use App\Helpers\CResponse;
use App\Helpers\Util\EnvUtil;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessSettingResource;
use App\Models\BusinessSetting;
use App\Rules\MobileNumberRule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class BusinessSettingController extends Controller
{
    public function index(): BusinessSettingResource
    {
        $business_setup = BusinessSetting::getInstance();
        return new BusinessSettingResource($business_setup);
    }


    /**
     * @throws ValidationException
     */
    public function updateAppSetting(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }

        $validated_data = $this->validate($request, [
            BusinessSetting::$customer_android_app_min_version => ['required', 'numeric'],
            BusinessSetting::$customer_ios_app_min_version => ['required', 'numeric'],
            BusinessSetting::$customer_android_app_url => [],
            BusinessSetting::$customer_ios_app_url => [],

            BusinessSetting::$delivery_boy_android_app_min_version => ['required', 'numeric'],
            BusinessSetting::$delivery_boy_ios_app_min_version => ['required', 'numeric'],
            BusinessSetting::$delivery_boy_android_app_url => [],
            BusinessSetting::$delivery_boy_ios_app_url => [],

            BusinessSetting::$seller_android_app_min_version => ['required', 'numeric'],
            BusinessSetting::$seller_ios_app_min_version => ['required', 'numeric'],
            BusinessSetting::$seller_android_app_url => [],
            BusinessSetting::$seller_ios_app_url => [],
        ]);

        BusinessHelper::setData($validated_data);

        return $this->successResponse('App settings is updated');
    }

    /**
     * @throws ValidationException
     */
    public function updateInfoSetting(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }

        $validated_data = $this->validate($request, [
            BusinessSetting::$about_us => [],

            BusinessSetting::$instagram_url => [],
            BusinessSetting::$whatsapp_url => [],
            BusinessSetting::$facebook_url => [],
            BusinessSetting::$twitter_url => [],
            BusinessSetting::$linkedin_url => [],
            BusinessSetting::$pinterest_url => [],

            BusinessSetting::$business_name => [],
            BusinessSetting::$first_name => [],
            BusinessSetting::$last_name => [],
            BusinessSetting::$email => ['email'],
            BusinessSetting::$mobile_number => [new MobileNumberRule()],

            BusinessSetting::$address => [],
            BusinessSetting::$city => [],
            BusinessSetting::$state => [],
            BusinessSetting::$country => [],
            BusinessSetting::$pincode => [],

            BusinessSetting::$address_latitude => ['numeric'],
            BusinessSetting::$address_longitude => ['numeric'],

            BusinessSetting::$terms_conditions => [],
            BusinessSetting::$privacy_policies => [],

        ]);
        BusinessHelper::setData($validated_data);

        return $this->successResponse('Info settings is updated');
    }

    /**
     * @throws ValidationException
     */
    public function updateOrderSetting(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }

        $validated_data = $this->validate($request, [
            BusinessSetting::$new_order_for_seller_notification => [],
            BusinessSetting::$order_payment_done_for_seller_notification => [],
            BusinessSetting::$cancel_by_customer_for_seller_notification => [],
            BusinessSetting::$cancel_by_seller_for_customer_notification => [],
            BusinessSetting::$order_reject_for_customer_notification => [],

            BusinessSetting::$order_resubmit_for_seller_notification => [],
            BusinessSetting::$order_accept_for_customer_notification => [],

            BusinessSetting::$order_processing_for_customer_notification => [],
            BusinessSetting::$order_processing_for_delivery_boy_notification => [],

            BusinessSetting::$order_estimate_time_change_for_customer_notification => [],


            BusinessSetting::$assign_order_for_delivery_boy_notification => [],
            BusinessSetting::$delivery_boy_reject_for_seller_notification => [],
            BusinessSetting::$delivery_boy_accept_for_seller_notification => [],
            BusinessSetting::$delivery_boy_accept_for_customer_notification => [],

            BusinessSetting::$order_ready_for_customer_notification => [],
            BusinessSetting::$order_ready_for_delivery_boy_notification => [],
            BusinessSetting::$out_for_delivery_customer_notification => [],
            BusinessSetting::$order_delivered_for_customer_notification => [],
            BusinessSetting::$order_delivered_for_seller_notification => [],

            BusinessSetting::$enable_admin_order_notification => ['boolean'],

            BusinessSetting::$order_accept_for_customer_sms => [],

            BusinessSetting::$enable_delivery_otp_verification_for_delivery_boy => ['boolean'],
            BusinessSetting::$auto_assign_delivery_boy => ['boolean'],
            BusinessSetting::$max_order_assign_to_delivery_boy => ['numeric', 'min:1'],
            BusinessSetting::$can_delivery_boy_reject_order => ['boolean'],
            BusinessSetting::$delivery_charge_multiplier => ['numeric', 'min:0'],
            BusinessSetting::$minimum_delivery_charge => ['numeric', 'min:0'],


            BusinessSetting::$delivery_commission => ['required', 'numeric', 'min:0'],
            BusinessSetting::$payment_charge => ['required', 'numeric', 'min:0'],
            BusinessSetting::$delivery_commission_type => ['required', 'in:percent,amount'],
            BusinessSetting::$payment_charge_type => ['required', 'in:percent,amount'],

        ]);
        BusinessHelper::setData($validated_data);

        return $this->successResponse('Order settings is updated');
    }

    /**
     * @throws ValidationException
     */
    public function updatePaymentSetting(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }

        $validated_data = $this->validate($request, [
            BusinessSetting::$payment_razorpay_enable => ['boolean',],
            BusinessSetting::$payment_razorpay_id => ['required_if:' . BusinessSetting::$payment_razorpay_enable . ',true'],
            BusinessSetting::$payment_razorpay__secret_key => ['required_if:' . BusinessSetting::$payment_razorpay_enable . ',true'],

            BusinessSetting::$payment_stripe_enable => ['boolean',],
            BusinessSetting::$payment_stripe_publishable_key => ['required_if:' . BusinessSetting::$payment_stripe_enable . ',true'],
            BusinessSetting::$payment_stripe__secret_key => ['required_if:' . BusinessSetting::$payment_stripe_enable . ',true'],

            BusinessSetting::$payment_paypal_enable => ['boolean',],
            BusinessSetting::$payment_paypal_client_id => ['required_if:' . BusinessSetting::$payment_paypal_enable . ',true'],
            BusinessSetting::$payment_paypal__secret_key => ['required_if:' . BusinessSetting::$payment_paypal_enable . ',true'],

            BusinessSetting::$payment_paystack_enable => ['boolean',],
            BusinessSetting::$payment_paystack_public_key => ['required_if:' . BusinessSetting::$payment_paystack_enable . ',true'],
            BusinessSetting::$payment_paystack__secret_key => ['required_if:' . BusinessSetting::$payment_paystack_enable . ',true'],

            BusinessSetting::$payment_flutterwave_enable => ['boolean',],
            BusinessSetting::$payment_flutterwave_public_key => ['required_if:' . BusinessSetting::$payment_flutterwave_enable . ',true'],
            BusinessSetting::$payment_flutterwave__secret_key => ['required_if:' . BusinessSetting::$payment_flutterwave_enable . ',true'],

            BusinessSetting::$payment_cashfree_enable => ['boolean',],
            BusinessSetting::$payment_cashfree_app_id => ['required_if:' . BusinessSetting::$payment_cashfree_enable . ',true'],
            BusinessSetting::$payment_cashfree__secret_key => ['required_if:' . BusinessSetting::$payment_cashfree_enable . ',true'],
        ]);

        BusinessHelper::setData($validated_data);

        return $this->successResponse('Payment settings is updated');
    }

    /**
     * @throws ValidationException
     */
    public function updateMailSetting(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }

        $validated_data = $this->validate($request, [
            BusinessSetting::$mail_driver => ['required',],
            BusinessSetting::$mail_host => ['required',],
            BusinessSetting::$mail_port => ['required',],
            BusinessSetting::$mail_username => ['required',],
            BusinessSetting::$mail_password => ['required',],
            BusinessSetting::$mail_encryption => ['required',],
            BusinessSetting::$mail_from_email => ['required',],
            BusinessSetting::$mail_from_name => ['required',],
        ]);

        EnvUtil::changeEnvVariable('MAIL_MAILER', $validated_data['mail_driver']);
        EnvUtil::changeEnvVariable('MAIL_HOST', $validated_data['mail_host']);
        EnvUtil::changeEnvVariable('MAIL_PORT', $validated_data['mail_port']);
        EnvUtil::changeEnvVariable('MAIL_USERNAME', $validated_data['mail_username']);
        EnvUtil::changeEnvVariable('MAIL_PASSWORD', $validated_data['mail_password']);
        EnvUtil::changeEnvVariable('MAIL_ENCRYPTION', $validated_data['mail_encryption']);
        EnvUtil::changeEnvVariable('MAIL_FROM_ADDRESS', $validated_data['mail_from_email']);
        EnvUtil::changeEnvVariable('MAIL_FROM_NAME', $validated_data['mail_from_name']);


        BusinessHelper::setData($validated_data);

        return $this->successResponse('Mail settings is updated');
    }

    /**
     * @throws ValidationException
     */
    public function updateSmsSetting(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }

        $validated_data = $this->validate($request, [
            BusinessSetting::$sms_twilio_enable => ['boolean',],
            BusinessSetting::$sms_twilio_sid => ['required_if:' . BusinessSetting::$sms_twilio_enable . ',true'],
            BusinessSetting::$sms_twilio_token => ['required_if:' . BusinessSetting::$sms_twilio_enable . ',true'],
            BusinessSetting::$sms_twilio_mobile_number => [
                'required_if:' . BusinessSetting::$sms_twilio_enable . ',true',
                new MobileNumberRule()
            ],
        ]);

        BusinessHelper::setData($validated_data);

        return $this->successResponse('Sms settings is updated');
    }



    /**
     * @throws ValidationException
     */
    public function updateSystemSetting(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }
        $validated_data = $this->validate($request, [
            BusinessSetting::$currency_symbol => ['required'],
            BusinessSetting::$currency_code => ['required'],
            BusinessSetting::$currency_position => ['required', 'in:left,right'],
            BusinessSetting::$time_format => ['required', 'in:12h,24h'],
            BusinessSetting::$digit_after_decimal => ['required', 'numeric'],
            BusinessSetting::$can_shop_self_register => ['boolean'],
            BusinessSetting::$can_delivery_boy_self_register => ['boolean'],


            BusinessSetting::$customer_mobile_number_verification => ['boolean'],
            BusinessSetting::$customer_email_verification => ['boolean'],
            BusinessSetting::$customer_referral_amount => ['numeric'],

            BusinessSetting::$customer_order_loyalty_enable => ['boolean'],
            BusinessSetting::$customer_order_loyalty_amount => [
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (request()->get(
                            BusinessSetting::$customer_order_loyalty_amount_type
                        ) == 'percent' && $value > 100) {
                        $fail('Percentage discount is not more than 100');
                    }
                },
            ],
            BusinessSetting::$customer_order_loyalty_amount_type => [
                'required_with:' . BusinessSetting::$customer_order_loyalty_amount,
                'in:percent,amount',
            ],
            BusinessSetting::$customer_minimum_loyalty_point_to_convert => ['numeric', 'min:0'],
            BusinessSetting::$customer_loyalty_to_wallet_multiplier => ['numeric', 'min:1'],


        ]);

        BusinessHelper::setData($validated_data);

        return $this->successResponse('System settings is updated');
    }


    /**
     * @throws ValidationException
     */
    public function updateOtherSetting(Request $request): Response|Application|ResponseFactory
    {
        if (env('DEMO', false)) {
            return CResponse::demoError();
        }

        $validated_data = $this->validate($request, [
            BusinessSetting::$fcm_server_key => [],
            BusinessSetting::$mapbox_api_key => [],
            BusinessSetting::$google_map_api_key => [],
            BusinessSetting::$recaptcha_enable => ['boolean',],
            BusinessSetting::$recaptcha_site_key => ['required_if:' . BusinessSetting::$recaptcha_enable . ',true'],
            BusinessSetting::$recaptcha_secret_key => [],
            BusinessSetting::$google_analytics_enable => ['boolean',],
            BusinessSetting::$google_analytics_id => ['required_if:' . BusinessSetting::$google_analytics_enable . ',true'],
        ]);

        BusinessHelper::setData($validated_data);

        return $this->successResponse('Other settings is updated');
    }


}

