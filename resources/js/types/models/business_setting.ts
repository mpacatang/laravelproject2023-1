import {IModel} from "@js/types/models/models";
import {ChargesTypes} from "@js/types/custom_types";
import {IModule, Module} from "@js/types/models/module";

export interface ICurrency extends  IModel{
    symbol: string;
    code: string;
    name: string

}


export interface ISystemSetting extends IModel {
    currency_code: string,
    currency_symbol: string,
    currency_position: 'left' | 'right',
    time_format: '12h' | '24h',
    digit_after_decimal: number,
    can_shop_self_register: boolean,
    can_delivery_boy_self_register: boolean,


    customer_email_verification: boolean,
    customer_mobile_number_verification: boolean,
    customer_referral_amount: number

    customer_order_loyalty_enable: boolean,
    customer_order_loyalty_amount: number,
    customer_order_loyalty_amount_type: ChargesTypes,
    customer_minimum_loyalty_point_to_convert: number,
    customer_loyalty_to_wallet_multiplier: number,


}

export interface IAppSetting extends IModel {
    customer_android_app_min_version: number,
    customer_ios_app_min_version: number,
    customer_android_app_url: string,
    customer_ios_app_url: string,
    seller_android_app_min_version: number,
    seller_ios_app_min_version: number,
    seller_android_app_url: string,
    seller_ios_app_url: string,
    delivery_boy_android_app_min_version: number,
    delivery_boy_ios_app_min_version: number,
    delivery_boy_android_app_url: string,
    delivery_boy_ios_app_url: string,
}

export interface IPaymentSetting extends IModel {
    payment_razorpay_enable: boolean,
    payment_razorpay_id: string,
    payment_razorpay__secret_key: string,
    payment_stripe_enable: boolean,
    payment_stripe_publishable_key: string,
    payment_stripe__secret_key: string,
    payment_paypal_enable: boolean,
    payment_paypal_client_id: string,
    payment_paypal__secret_key: string,

    payment_paystack_enable: boolean,
    payment_paystack_public_key: string,
    payment_paystack__secret_key: string,

    payment_flutterwave_enable: boolean,
    payment_flutterwave_public_key: string,
    payment_flutterwave__secret_key: string,

    payment_cashfree_enable: boolean,
    payment_cashfree_app_id: string,
    payment_cashfree__secret_key: string,
}


export interface ISmsSetting extends IModel {
    sms_twilio_enable: boolean,
    sms_twilio_sid: string,
    sms_twilio_token: string,
    sms_twilio_mobile_number: string,
}

export interface IMailSetting extends IModel {
    mail_driver: string,
    mail_host: string,
    mail_port: string,
    mail_encryption: string,
    mail_username: string,
    mail_password: string,
    mail_from_email: string,
    mail_from_name: string,
}


export interface IOtherSetting extends IModel {
    fcm_server_key: string,

    mapbox_api_key: string,
    google_map_api_key: string,

    recaptcha_enable: boolean,
    recaptcha_site_key: string,
    recaptcha_secret_key: string,

    google_analytics_enable: boolean,
    google_analytics_id: string,
}


export interface IInfoSetting extends IModel {
    business_name: string,
    first_name: string,
    last_name: string,
    mobile_number: string,
    email: string,
    address: string,
    city: string,
    state: string,
    country: string,
    pincode: string,
    address_latitude: number,
    address_longitude: number,
    about_us: string,
    instagram_url: string,
    whatsapp_url: string,
    facebook_url: string,
    twitter_url: string,
    linkedin_url: string,
    pinterest_url: string,
    terms_conditions: string,
    privacy_policies: string
}


export interface IOrderSetting extends IModel {


    new_order_for_seller_notification: string,
    order_payment_done_for_seller_notification: string,
    cancel_by_customer_for_seller_notification: string,
    cancel_by_seller_for_customer_notification: string,

    order_reject_for_customer_notification: string,
    order_resubmit_for_seller_notification: string,
    order_accept_for_customer_notification: string,

    order_processing_for_customer_notification: string,
    order_processing_for_delivery_boy_notification: string,

    order_estimate_time_change_for_customer_notification: string,

    assign_order_for_delivery_boy_notification: string,
    delivery_boy_reject_for_seller_notification: string,
    delivery_boy_accept_for_seller_notification: string,
    delivery_boy_accept_for_customer_notification: string,

    order_ready_for_customer_notification: string,
    order_ready_for_delivery_boy_notification: string,

    out_for_delivery_notification: string,

    order_delivered_for_customer_notification: string,
    order_delivered_for_seller_notification: string,

    order_accept_for_customer_sms: string,



    enable_admin_order_notification: boolean
    enable_delivery_otp_verification_for_delivery_boy: boolean,
    auto_assign_delivery_boy: boolean,
    max_order_assign_to_delivery_boy: number,
    can_delivery_boy_reject_order: boolean,
    minimum_delivery_charge: number,
    delivery_charge_multiplier: number,
    order_commission: number,
    delivery_commission: number,
    payment_charge: number,
    order_commission_type: ChargesTypes,
    delivery_commission_type: ChargesTypes,
    payment_charge_type: ChargesTypes,





}


export interface IBusinessSetting extends ISystemSetting, IInfoSetting, ISmsSetting, IAppSetting, IOrderSetting, IOtherSetting {
    modules: IModule[],
    demo: boolean
}



export class BusinessSetting {

    static instance: IBusinessSetting = null;

    static getInstance(): IBusinessSetting | null {
        if (this.instance != null) {
            let bs = this.instance;


            return {
                ...bs,
                minimum_delivery_charge: parseFloat(bs.minimum_delivery_charge?.toString()),
                delivery_charge_multiplier: parseFloat(bs.delivery_charge_multiplier?.toString()),
            };
        }
        return null;

    }


    static init() {
        const bs = document.querySelector('meta[type=business_setting]');
        const content = bs?.getAttribute('content')
        if (content != null) {
            this.instance = JSON.parse(content);
            bs.remove();
        }

        Module.init();
    }


    static calculateOrderCommissionFromOrder(setting: IBusinessSetting, order: number): number {
        if (setting.order_commission_type === 'percent') {
            return (setting.order_commission * order / 100);
        }
        return setting.order_commission;
    }

    static calculateDeliveryCommissionFromOrder(setting: IBusinessSetting, order: number): number {
        if (setting.delivery_commission_type === 'percent') {
            return (setting.delivery_commission * order / 100);
        }
        return setting.delivery_commission;
    }

    static calculatePaymentChargeFromOrder(setting: IBusinessSetting, order: number): number {
        if (setting.payment_charge_type === 'percent') {
            return (setting.payment_charge * order / 100);
        }
        return setting.payment_charge;
    }

    static calculateChargeFromAmountAndType(amount: number, charge: number, type: ChargesTypes) {
        if (type === 'amount' || charge === 0) {
            return charge;
        }
        return (charge * amount / 100);
    }

}
