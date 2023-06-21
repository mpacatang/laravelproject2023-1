<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;


/**
 * @property mixed $key
 * @property mixed $value
 */
class BusinessSetting extends BaseModel
{
    use HasFactory;

    //// ======== Keys  ---- All are unique in database --- when update config it replace to new value  ===========================================//

    protected $visible = ['key', 'value'];

    ///----------- Commissions ------------------------//

    public static string $delivery_commission = 'delivery_commission';
    public static string $delivery_commission_type = 'delivery_commission_type';
    public static string $payment_charge = 'payment_charge';
    public static string $payment_charge_type = 'payment_charge_type';

    ///----------- App Setting ------------------------//

    public static string $customer_android_app_min_version = 'customer_android_app_min_version';
    public static string $customer_ios_app_min_version = 'customer_ios_app_min_version';
    public static string $customer_android_app_url = 'customer_android_app_url';
    public static string $customer_ios_app_url = 'customer_ios_app_url';

    public static string $delivery_boy_android_app_min_version = 'delivery_boy_android_app_min_version';
    public static string $delivery_boy_ios_app_min_version = 'delivery_boy_ios_app_min_version';
    public static string $delivery_boy_android_app_url = 'delivery_boy_android_app_url';
    public static string $delivery_boy_ios_app_url = 'delivery_boy_ios_app_url';

    public static string $seller_android_app_min_version = 'seller_android_app_min_version';
    public static string $seller_ios_app_min_version = 'seller_ios_app_min_version';
    public static string $seller_android_app_url = 'seller_android_app_url';
    public static string $seller_ios_app_url = 'seller_ios_app_url';


    ///----------- Order Setting ------------------------//
    ///
    //Notifications

    public static string $enable_admin_order_notification = 'enable_admin_order_notification';


    public static string $new_order_for_seller_notification = 'new_order_for_seller_notification';
    public static string $order_payment_done_for_seller_notification = 'order_payment_done_for_seller_notification';
    public static string $cancel_by_customer_for_seller_notification = 'cancel_by_customer_for_seller_notification';
    public static string $cancel_by_seller_for_customer_notification = 'cancel_by_seller_for_customer_notification';

    public static string $order_reject_for_customer_notification = 'order_reject_for_customer_notification';
    public static string $order_resubmit_for_seller_notification = 'order_resubmit_for_seller_notification';
    public static string $order_accept_for_customer_notification = 'order_accept_for_customer_notification';

    public static string $order_processing_for_customer_notification = 'order_processing_for_customer_notification';
    public static string $order_estimate_time_change_for_customer_notification = 'order_estimate_time_change_for_customer_notification';
    public static string $order_processing_for_delivery_boy_notification = 'order_processing_for_delivery_boy_notification';

    public static string $assign_order_for_delivery_boy_notification = 'assign_order_for_delivery_boy_notification';
    public static string $delivery_boy_reject_for_seller_notification = 'delivery_boy_reject_for_seller_notification';
    public static string $delivery_boy_accept_for_seller_notification = 'delivery_boy_accept_for_seller_notification';
    public static string $delivery_boy_accept_for_customer_notification = 'delivery_boy_accept_for_customer_notification';

    public static string $order_ready_for_customer_notification = 'order_ready_for_customer_notification';
    public static string $order_ready_for_delivery_boy_notification = 'order_ready_for_delivery_boy_notification';

    public static string $out_for_delivery_customer_notification = 'out_for_delivery_customer_notification';

    public static string $order_delivered_for_customer_notification = 'order_delivered_for_customer_notification';
    public static string $order_delivered_for_seller_notification = 'order_delivered_for_seller_notification';

    //Short Message (SMS)
    public static string $order_accept_for_customer_sms = 'order_accept_for_customer_sms';


    //Other
    public static string $enable_delivery_otp_verification_for_delivery_boy = 'enable_delivery_otp_verification_for_delivery_boy';
    public static string $auto_assign_delivery_boy = 'auto_assign_delivery_boy';
    public static string $max_order_assign_to_delivery_boy = 'max_order_assign_to_delivery_boy';
    public static string $can_delivery_boy_reject_order = 'can_delivery_boy_reject_order';
    public static string $minimum_delivery_charge = 'minimum_delivery_charge';
    public static string $delivery_charge_multiplier = 'delivery_charge_multiplier';

    public static string $customer_order_loyalty_enable = 'customer_order_loyalty_enable';
    public static string $customer_order_loyalty_amount = 'customer_order_loyalty_amount';
    public static string $customer_order_loyalty_amount_type = 'customer_order_loyalty_amount_type';
    public static string $customer_minimum_loyalty_point_to_convert = 'customer_minimum_loyalty_point_to_convert';
    public static string $customer_loyalty_to_wallet_multiplier = 'customer_loyalty_to_wallet_multiplier';


    ///----------- Customer ------------------------//

    public static string $customer_mobile_number_verification = 'customer_mobile_number_verification';
    public static string $customer_email_verification = 'customer_email_verification';


    public static string $terms_conditions = 'terms_conditions';
    public static string $privacy_policies = 'privacy_policies';

    public static string $about_us = 'about_us';

    ///-------------- System Setup ===============================//

    public static string $can_shop_self_register = 'can_shop_self_register';
    public static string $can_delivery_boy_self_register = 'can_delivery_boy_self_register';


    ///Personal Info
    public static string $business_name = 'business_name';
    public static string $first_name = 'first_name';
    public static string $last_name = 'last_name';
    public static string $mobile_number = 'mobile_number';
    public static string $email = 'email';

    public static string $address = 'address';
    public static string $city = 'city';
    public static string $state = 'state';
    public static string $country = 'country';
    public static string $pincode = 'pincode';
    public static string $address_latitude = 'address_latitude';
    public static string $address_longitude = 'address_longitude';

    public static string $instagram_url = 'instagram_url';
    public static string $whatsapp_url = 'whatsapp_url';
    public static string $facebook_url = 'facebook_url';
    public static string $twitter_url = 'twitter_url';
    public static string $linkedin_url = 'linkedin_url';
    public static string $pinterest_url = 'pinterest_url';

    public static string $currency_code = 'currency_code';
    public static string $currency_symbol = 'currency_symbol';
    public static string $currency_position = 'currency_position';
    public static string $time_format = 'time_format';
    public static string $digit_after_decimal = 'digit_after_decimal';

    public static string $customer_referral_amount = 'customer_referral_amount';

    /// ============= Careful (`__` double underscore = Private data which is not shared to any)=======================///

    /// FCM Notification
    public static string $fcm_server_key = 'fcm_server_key';   //Remain


    /// Payment
    public static string $payment_razorpay_enable = 'payment_razorpay_enable';
    public static string $payment_razorpay_id = 'payment_razorpay_id';
    public static string $payment_razorpay__secret_key = 'payment_razorpay__secret_key';

    public static string $payment_stripe_enable = 'payment_stripe_enable';
    public static string $payment_stripe_publishable_key = 'payment_stripe_publishable_key';
    public static string $payment_stripe__secret_key = 'payment_stripe__secret_key';

    public static string $payment_paypal_enable = 'payment_paypal_enable';
    public static string $payment_paypal_client_id = 'payment_paypal_client_id';
    public static string $payment_paypal__secret_key = 'payment_paypal__secret_key';

    public static string $payment_paystack_enable = 'payment_paystack_enable';
    public static string $payment_paystack_public_key = 'payment_paystack_public_key';
    public static string $payment_paystack__secret_key = 'payment_paystack__secret_key';

    public static string $payment_flutterwave_enable = 'payment_flutterwave_enable';
    public static string $payment_flutterwave_public_key = 'payment_flutterwave_public_key';
    public static string $payment_flutterwave__secret_key = 'payment_flutterwave__secret_key';

    public static string $payment_cashfree_enable = 'payment_cashfree_enable';
    public static string $payment_cashfree_app_id = 'payment_cashfree_app_id';
    public static string $payment_cashfree__secret_key = 'payment_cashfree__secret_key';

    public static string $payment_mercado_enable = 'payment_mercado_enable';
    public static string $payment_mercado_public_key = 'payment_mercado_public_key';
    public static string $payment_mercado__access_token = 'payment_mercado__access_token';


    /// SMS
    public static string $sms_twilio_enable = 'sms_twilio_enable';
    public static string $sms_twilio_sid = 'sms_twilio_sid';
    public static string $sms_twilio_token = 'sms_twilio_token';
    public static string $sms_twilio_mobile_number = 'sms_twilio_mobile_number';


    /// Recaptcha
    public static string $recaptcha_enable = 'recaptcha_enable';
    public static string $recaptcha_site_key = 'recaptcha_site_key';
    public static string $recaptcha_secret_key = 'recaptcha_secret_key';

    /// Map
    public static string $mapbox_api_key = 'mapbox_api_key';
    public static string $google_map_api_key = 'google_map_api_key';

    /// Mail
    public static string $mail_driver = 'mail_driver';
    public static string $mail_host = 'mail_host';
    public static string $mail_port = 'mail_port';
    public static string $mail_username = 'mail_username';
    public static string $mail_password = 'mail_password';
    public static string $mail_encryption = 'mail_encryption';
    public static string $mail_from_email = 'mail_from_email';
    public static string $mail_from_name = 'mail_from_name';

    /// Google Analytics
    public static string $google_analytics_enable = 'google_analytics_enable';
    public static string $google_analytics_id = 'google_analytics_id';


    /// ================= Defaults ========================================//

    public static array|null $instance = null;
    protected $guarded = [];

    public static function updateCache()
    {
        BusinessSetting::$instance = Arr::pluck(BusinessSetting::all()->toArray(), 'value', 'key');
    }

    public static function getInstance(): ?array
    {
        if (BusinessSetting::$instance == null) {
            BusinessSetting::$instance = Arr::pluck(BusinessSetting::all()->toArray(), 'value', 'key');
        }
        return BusinessSetting::$instance;
    }


    /// =================  Functions  ========================================//


    public static function _get($key, $default = ""): mixed
    {
        $data = self::getInstance();
        if (isset($data[$key])) {
            return $data[$key];
        }
        return $default;
    }

    public static function _set($key, $value): bool
    {
        $business_setup = BusinessSetting::where('key', $key)->first();
        if (!$business_setup) {
            $business_setup = new BusinessSetting();
            $business_setup->key = $key;
        }
        $business_setup->value = $value;
        return $business_setup->save();
    }


    /// ======================== Getter ====================================///

    public static function canDeliveryBoyRejectOrder()
    {
        return self::_get(self::$can_delivery_boy_reject_order, true);
    }

    public static function isEnableAdminOrderNotification()
    {
        return self::_get(self::$enable_admin_order_notification, true);
    }

    public static function isAutoAssignDeliveryBoy()
    {
        return self::_get(self::$auto_assign_delivery_boy, false);
    }

    public static function isOrderLoyaltyEnabled()
    {
        return self::_get(self::$customer_order_loyalty_enable, false);
    }

    public static function isEnableDeliveryOtpVerificationForDeliveryBoy()
    {
        return self::_get(self::$enable_delivery_otp_verification_for_delivery_boy, true);
    }

    public static function maxOrderAssignToDeliveryBoy()
    {
        return self::_get(self::$max_order_assign_to_delivery_boy, 1);
    }


    public static function getCustomerEmailVerification()
    {
        return self::_get(self::$customer_email_verification, false);
    }

    public static function getCustomerMobileNumberVerification()
    {
        return self::_get(self::$customer_mobile_number_verification, false);
    }


    public static function getCustomerReferralAmount()
    {
        return self::_get(self::$customer_referral_amount, 0);
    }

    /// Business
    public static function getCurrencyCode()
    {
        return self::_get(self::$currency_code,);
    }

    public static function getBusinessName()
    {
        return self::_get(self::$business_name);
    }

}
