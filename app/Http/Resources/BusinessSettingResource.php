<?php

namespace App\Http\Resources;

use App\Http\Resources\Strict\StrictBusinessSettingResource;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

/**
 */
class BusinessSettingResource extends StrictBusinessSettingResource
{


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */


    public function toArray($request): array
    {
        $guarded_keys = [
            BusinessSetting::$fcm_server_key,

            BusinessSetting::$payment_razorpay_id,
            BusinessSetting::$payment_razorpay__secret_key,

            BusinessSetting::$payment_stripe_publishable_key,
            BusinessSetting::$payment_stripe__secret_key,

            BusinessSetting::$payment_paypal_client_id,
            BusinessSetting::$payment_paypal__secret_key,

            BusinessSetting::$payment_paystack_public_key,
            BusinessSetting::$payment_paystack__secret_key,

            BusinessSetting::$payment_flutterwave_public_key,
            BusinessSetting::$payment_flutterwave__secret_key,

            BusinessSetting::$payment_cashfree_app_id,
            BusinessSetting::$payment_cashfree__secret_key,

            BusinessSetting::$payment_mercado_public_key,
            BusinessSetting::$payment_mercado__access_token,

            BusinessSetting::$sms_twilio_sid,
            BusinessSetting::$sms_twilio_token,
            BusinessSetting::$sms_twilio_mobile_number,

            BusinessSetting::$mapbox_api_key,


            BusinessSetting::$mail_driver,
            BusinessSetting::$mail_host,
            BusinessSetting::$mail_port,
            BusinessSetting::$mail_username,
            BusinessSetting::$mail_password,
            BusinessSetting::$mail_encryption,
            BusinessSetting::$mail_from_email,
            BusinessSetting::$mail_from_name,


            BusinessSetting::$google_analytics_id,

        ];

        $boolean_keys = [
            BusinessSetting::$sms_twilio_enable,
            BusinessSetting::$payment_razorpay_enable,
            BusinessSetting::$payment_stripe_enable,
            BusinessSetting::$payment_paypal_enable,
            BusinessSetting::$payment_paystack_enable,
            BusinessSetting::$payment_flutterwave_enable,
            BusinessSetting::$payment_cashfree_enable,
            BusinessSetting::$payment_mercado_enable,

            BusinessSetting::$google_analytics_enable,
        ];

        $data = [];
        foreach ($guarded_keys as $guarded_key) {
            $data[$guarded_key] = BusinessSetting::_get($guarded_key);
        }
        foreach ($boolean_keys as $boolean_key) {
            $data[$boolean_key] = filter_var(BusinessSetting::_get($boolean_key), FILTER_VALIDATE_BOOLEAN);
        }


        return [
            ...parent::toArray($request),
            ...$data
        ];


    }


}
