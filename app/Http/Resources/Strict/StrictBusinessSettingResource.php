<?php

namespace App\Http\Resources\Strict;

use App\Models\BusinessSetting;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $payment_charge
 * @property mixed $payment_charge_type
 * @property mixed $delivery_commission_type
 * @property mixed $order_commission
 * @property mixed $order_commission_type
 * @property mixed $tax
 * @property mixed $tax_type
 * @property mixed $delivery_commission
 * @property mixed $ios_app_url
 * @property mixed $android_app_url
 * @property mixed $ios_app_min_version
 * @property mixed $android_app_min_version
 * @method get(string $string)
 */
class StrictBusinessSettingResource extends JsonResource
{


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */


    public function toArray($request): array
    {

        $unguarded_keys = [

            ///----------- Commissions ------------------------//

            BusinessSetting::$delivery_commission, BusinessSetting::$delivery_commission_type,

            BusinessSetting::$payment_charge, BusinessSetting::$payment_charge_type,

            ///----------- App Setting ------------------------//


            BusinessSetting::$customer_android_app_min_version, BusinessSetting::$customer_ios_app_min_version, BusinessSetting::$customer_android_app_url, BusinessSetting::$customer_ios_app_url,

            BusinessSetting::$delivery_boy_android_app_min_version, BusinessSetting::$delivery_boy_ios_app_min_version, BusinessSetting::$delivery_boy_android_app_url, BusinessSetting::$delivery_boy_ios_app_url,

            BusinessSetting::$seller_android_app_min_version, BusinessSetting::$seller_ios_app_min_version, BusinessSetting::$seller_android_app_url, BusinessSetting::$seller_ios_app_url,

            ///----------- Order  ------------------------//
            ///
            ///Notifications
            BusinessSetting::$new_order_for_seller_notification, BusinessSetting::$order_payment_done_for_seller_notification,

            BusinessSetting::$cancel_by_customer_for_seller_notification, BusinessSetting::$cancel_by_seller_for_customer_notification,

            BusinessSetting::$order_reject_for_customer_notification, BusinessSetting::$order_resubmit_for_seller_notification, BusinessSetting::$order_accept_for_customer_notification,

            BusinessSetting::$order_processing_for_customer_notification, BusinessSetting::$order_estimate_time_change_for_customer_notification, BusinessSetting::$order_processing_for_delivery_boy_notification,

            BusinessSetting::$assign_order_for_delivery_boy_notification, BusinessSetting::$delivery_boy_reject_for_seller_notification, BusinessSetting::$delivery_boy_accept_for_seller_notification, BusinessSetting::$delivery_boy_accept_for_customer_notification,


            BusinessSetting::$order_ready_for_customer_notification, BusinessSetting::$order_ready_for_delivery_boy_notification,

            BusinessSetting::$out_for_delivery_customer_notification, BusinessSetting::$order_delivered_for_customer_notification, BusinessSetting::$order_delivered_for_seller_notification,

            //SMS
            BusinessSetting::$order_accept_for_customer_sms,

            ///Other
            BusinessSetting::$max_order_assign_to_delivery_boy, BusinessSetting::$minimum_delivery_charge, BusinessSetting::$delivery_charge_multiplier, BusinessSetting::$customer_order_loyalty_amount, BusinessSetting::$customer_order_loyalty_amount_type, BusinessSetting::$customer_minimum_loyalty_point_to_convert, BusinessSetting::$customer_loyalty_to_wallet_multiplier,


            ///----------- All ------------------------//


            BusinessSetting::$privacy_policies, BusinessSetting::$terms_conditions, BusinessSetting::$about_us,


            //---------- System Settings --------------------//
            BusinessSetting::$business_name, BusinessSetting::$first_name, BusinessSetting::$last_name, BusinessSetting::$mobile_number, BusinessSetting::$email,

            BusinessSetting::$address, BusinessSetting::$city, BusinessSetting::$state, BusinessSetting::$country, BusinessSetting::$pincode, BusinessSetting::$address_latitude, BusinessSetting::$address_longitude,

            BusinessSetting::$instagram_url, BusinessSetting::$whatsapp_url, BusinessSetting::$facebook_url, BusinessSetting::$twitter_url, BusinessSetting::$linkedin_url, BusinessSetting::$pinterest_url,

            BusinessSetting::$currency_symbol, BusinessSetting::$currency_code, BusinessSetting::$currency_position, BusinessSetting::$time_format, BusinessSetting::$digit_after_decimal,

            BusinessSetting::$google_map_api_key,

            BusinessSetting::$customer_referral_amount, BusinessSetting::$recaptcha_site_key,];

        $numeric_keys = [

        ];

        $boolean_keys = [BusinessSetting::$enable_delivery_otp_verification_for_delivery_boy, BusinessSetting::$auto_assign_delivery_boy, BusinessSetting::$enable_admin_order_notification, BusinessSetting::$can_delivery_boy_reject_order, BusinessSetting::$customer_mobile_number_verification, BusinessSetting::$can_shop_self_register, BusinessSetting::$customer_order_loyalty_enable, BusinessSetting::$can_delivery_boy_self_register, BusinessSetting::$recaptcha_enable,];

        $data = [];
        foreach ($unguarded_keys as $unguarded_key) {
            $data[$unguarded_key] = BusinessSetting::_get($unguarded_key);
        }

        foreach ($boolean_keys as $boolean_key) {
            $data[$boolean_key] = filter_var(BusinessSetting::_get($boolean_key), FILTER_VALIDATE_BOOLEAN);
        }

        return [...$data, 'modules' => StrictModuleResource::collection(Module::active()->get()),
            'demo' => env('DEMO', false)];
    }

}
