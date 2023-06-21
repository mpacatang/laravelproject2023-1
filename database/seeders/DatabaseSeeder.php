<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Admin;
use App\Models\AdminRole;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerFavoriteProduct;
use App\Models\CustomerWallet;
use App\Models\CustomerWalletTransaction;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyReview;
use App\Models\HomeBanner;
use App\Models\HomeLayout;
use App\Models\Module;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\Seller;
use App\Models\SellerRole;
use App\Models\Shop;
use App\Models\ShopPlan;
use App\Models\ShopPlanHistory;
use App\Models\ShopReview;
use App\Models\ShopTime;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    public static function run(): void
    {
        $business_setups = [
            ///----------- Commissions ------------------------//

            [
                'key' => BusinessSetting::$delivery_commission_type,
                'value' => 'percent'
            ], [
                'key' => BusinessSetting::$payment_charge_type,
                'value' => 'percent'
            ], [
                'key' => BusinessSetting::$delivery_commission,
                'value' => 10
            ], [
                'key' => BusinessSetting::$payment_charge,
                'value' => 10
            ],

            ///----------- App Setting ------------------------//

            [
                'key' => BusinessSetting::$customer_android_app_min_version,
                'value' => 1
            ],

            [
                'key' => BusinessSetting::$customer_ios_app_min_version,
                'value' => 1
            ],

            [
                'key' => BusinessSetting::$customer_android_app_url,
                'value' => null
            ],
            [
                'key' => BusinessSetting::$customer_ios_app_url,
                'value' => null
            ],

            [
                'key' => BusinessSetting::$seller_android_app_min_version,
                'value' => 1
            ],

            [
                'key' => BusinessSetting::$seller_ios_app_min_version,
                'value' => 1
            ],

            [
                'key' => BusinessSetting::$seller_android_app_url,
                'value' => null
            ],
            [
                'key' => BusinessSetting::$seller_ios_app_url,
                'value' => null
            ],

            [
                'key' => BusinessSetting::$delivery_boy_android_app_min_version,
                'value' => 1
            ],

            [
                'key' => BusinessSetting::$delivery_boy_ios_app_min_version,
                'value' => 1
            ],

            [
                'key' => BusinessSetting::$delivery_boy_android_app_url,
                'value' => null
            ],
            [
                'key' => BusinessSetting::$delivery_boy_ios_app_url,
                'value' => null
            ],

            ///----------- Order Notifications ------------------------//
            [
                'key' => BusinessSetting::$new_order_for_seller_notification,
                'value' => 'Hurray!!! New order received'
            ], [
                'key' => BusinessSetting::$order_payment_done_for_seller_notification,
                'value' => 'Order payment is done by customer'
            ],
            [
                'key' => BusinessSetting::$cancel_by_customer_for_seller_notification,
                'value' => 'OOps!! Customer cancelled order'
            ],

            [
                'key' => BusinessSetting::$cancel_by_seller_for_customer_notification,
                'value' => 'Seller cancelled your order. Still you can order something else'
            ],
            [
                'key' => BusinessSetting::$order_reject_for_customer_notification,
                'value' => 'Your order is reject by shop. Please do needful'
            ],

            [
                'key' => BusinessSetting::$order_resubmit_for_seller_notification,
                'value' => 'Order is resubmitted. Take a look and accept'
            ],

            [
                'key' => BusinessSetting::$order_accept_for_customer_notification,
                'value' => 'Your order is confirmed'
            ],

            [
                'key' => BusinessSetting::$order_processing_for_customer_notification,
                'value' => 'Your order is in processing'
            ],

            [
                'key' => BusinessSetting::$order_processing_for_delivery_boy_notification,
                'value' => 'Order is in processing, need to arrive at shop'
            ],

            [
                'key' => BusinessSetting::$order_estimate_time_change_for_customer_notification,
                'value' => 'Order estimate time changed'
            ],

            [
                'key' => BusinessSetting::$assign_order_for_delivery_boy_notification,
                'value' => 'Hurray!!! New order assigned to you'
            ],

            [
                'key' => BusinessSetting::$delivery_boy_reject_for_seller_notification,
                'value' => 'Order is reject by delivery'
            ],

            [
                'key' => BusinessSetting::$delivery_boy_accept_for_seller_notification,
                'value' => 'Your order accepted by delivery to deliver'
            ],

            [
                'key' => BusinessSetting::$delivery_boy_accept_for_customer_notification,
                'value' => 'Delivery boy is assigned for your order'
            ],

            [
                'key' => BusinessSetting::$order_ready_for_customer_notification,
                'value' => 'Your order is ready to deliver'
            ],

            [
                'key' => BusinessSetting::$order_ready_for_delivery_boy_notification,
                'value' => 'Order is ready to deliver, Need to arrive at shop ASAP'
            ],

            [
                'key' => BusinessSetting::$out_for_delivery_customer_notification,
                'value' => 'Out for delivery. We deliver ASAP'
            ],
            [
                'key' => BusinessSetting::$order_delivered_for_customer_notification,
                'value' => 'Your order is deliver. Please rate us'
            ],
            [
                'key' => BusinessSetting::$order_delivered_for_seller_notification,
                'value' => 'Order deliver successfully'
            ],
            [
                'key' => BusinessSetting::$enable_admin_order_notification,
                'value' => true
            ],
            [
                'key' => BusinessSetting::$enable_delivery_otp_verification_for_delivery_boy,
                'value' => true
            ],
            [
                'key' => BusinessSetting::$auto_assign_delivery_boy,
                'value' => true
            ],
            [
                'key' => BusinessSetting::$max_order_assign_to_delivery_boy,
                'value' => 4
            ],
            [
                'key' => BusinessSetting::$can_delivery_boy_reject_order,
                'value' => true
            ],
            [
                'key' => BusinessSetting::$minimum_delivery_charge,
                'value' => 5
            ],
            [
                'key' => BusinessSetting::$delivery_charge_multiplier,
                'value' => 3
            ],


            //---------- System Settings --------------------//

            [
                'key' => BusinessSetting::$can_shop_self_register,
                'value' => true
            ],

            [
                'key' => BusinessSetting::$business_name,
                'value' => 'shopy'
            ],
            [
                'key' => BusinessSetting::$first_name,
                'value' => 'Jim'
            ],
            [
                'key' => BusinessSetting::$last_name,
                'value' => 'Coakley'
            ],
            [
                'key' => BusinessSetting::$mobile_number,
                'value' => '+19876543210'
            ],
            [
                'key' => BusinessSetting::$email,
                'value' => 'jim.scoalk@shopy.com'
            ],
            [
                'key' => BusinessSetting::$address,
                'value' => '4148 Shady Pines Drive',
            ],

            [
                'key' => BusinessSetting::$city,
                'value' => 'Houston'
            ],
            [
                'key' => BusinessSetting::$state,
                'value' => 'Texas'
            ],
            [
                'key' => BusinessSetting::$country,
                'value' => 'USA'
            ],
            [
                'key' => BusinessSetting::$pincode,
                'value' => '24333'
            ],
            [
                'key' => BusinessSetting::$address_latitude,
                'value' => 31.473108
            ],
            [
                'key' => BusinessSetting::$address_longitude,
                'value' => -99.160169
            ],
            [
                'key' => BusinessSetting::$about_us,
                'value' => '<p>This is demo about us. <i>Admin can change it from panel</i></p>'
            ],
            [
                'key' => BusinessSetting::$instagram_url,
                'value' => 'https://www.instagram.com/'
            ],
            [
                'key' => BusinessSetting::$whatsapp_url,
                'value' => 'wa://+12762363736'
            ],
            [
                'key' => BusinessSetting::$facebook_url,
                'value' => 'https://www.facebook.com/'
            ],
            [
                'key' => BusinessSetting::$twitter_url,
                'value' => 'https://twitter.com/'
            ],
            [
                'key' => BusinessSetting::$linkedin_url,
                'value' => 'https://www.linkedin.com/'
            ],
            [
                'key' => BusinessSetting::$pinterest_url,
                'value' => 'https://pinterest.com/'
            ],

            [
                'key' => BusinessSetting::$customer_referral_amount,
                'value' => 50
            ],

            [
                'key' => BusinessSetting::$currency_code,
                'value' => "USD"

            ],
            [
                'key' => BusinessSetting::$currency_symbol,
                'value' => "$"

            ],
            [
                'key' => BusinessSetting::$currency_position,
                'value' => "left"
            ],
            [
                'key' => BusinessSetting::$time_format,
                'value' => "12h"
            ],
            [
                'key' => BusinessSetting::$digit_after_decimal,
                'value' => 2

            ],


            ///----------- Customer ------------------------//


            [
                'key' => BusinessSetting::$customer_email_verification,
                'value' => true
            ],
            [
                'key' => BusinessSetting::$customer_mobile_number_verification,
                'value' => true
            ],

            ///----------- All ------------------------//


            [
                'key' => BusinessSetting::$privacy_policies,
                'value' => '<p>This is demo policies. <b>Admin can change it from panel</b></p>'
            ],
            [
                'key' => BusinessSetting::$terms_conditions,
                'value' => '<p>This is demo terms. <i>Admin can change it from panel</i></p>'
            ],

        ];

        $modules = [
            [
                'type' => 'ecommerce',
                'title' => 'E-Commerce',
                'active' => true
            ],
            [
                'type' => 'grocery',
                'title' => 'Grocery',
                'active' => true
            ],
            [
                'type' => 'food',
                'title' => 'Food',
                'active' => true
            ],
            [
                'type' => 'pharmacy',
                'title' => 'Pharmacy',
                'active' => true
            ],
        ];

        foreach ($business_setups as $business_setup) {
            BusinessSetting::create($business_setup);
        }

        foreach ($modules as $module) {
            Module::create($module);
        }

    }

}
