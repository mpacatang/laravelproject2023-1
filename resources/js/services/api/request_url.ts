export class adminAPI {

    static auth = {
        login: 'admin/login/',
        logout: 'admin/logout/',
        google_login: 'admin/google_login/',
    }

    static init = {
        get: `admin/init/`,
    }


    static products = {
        ...adminAPI.getBaseURLs('products'),
        validate_import_data: `admin/products/validate_import_data`,
        import_data: `admin/products/import_data`,
        remove_availability: (id: number) => `admin/products/${id}/remove_availability`,
        reviews: (id: number) => `admin/products/${id}/reviews`,
    }


    static product_reviews = {
         delete: (id: number) => `admin/product_reviews/${id}/`,
    }


    static orders = {
        ...adminAPI.getBaseURLs('orders'),
        assign_delivery: (id: number) => `admin/orders/${id}/assign_delivery/`,

        cancel: (id: number) => `admin/orders/${id}/cancel/`,
        reject: (id: number) => `admin/orders/${id}/reject/`,
        accept: (id: number) => `admin/orders/${id}/accept/`,
        set_ready_at: (id: number) => `admin/orders/${id}/set_ready_at/`,
        ready: (id: number) => `admin/orders/${id}/ready/`,
        set_as_paid: (id: number) => `admin/orders/${id}/set_as_paid/`,
        deliver: (id: number) => `admin/orders/${id}/deliver/`,
    }


    static product_options = {
        ...adminAPI.getBaseURLs('product_options')
    }


    static product_images = {
        delete: (id: number) => `admin/product_images/${id}/`
    }


    static admins = {
        ...adminAPI.getBaseURLs('admins'),
        remove_avatar: (id: number) => `admin/admins/${id}/remove_avatar`

    }

    static sellers = {
        ...adminAPI.getBaseURLs('sellers'),
        available_owners: 'admin/sellers/available_owners',
        remove_avatar: (id: number) => `admin/sellers/${id}/remove_avatar`

    }

    static modules = {
        ...adminAPI.getBaseURLs('modules')
    }
    static subscribers = {
        ...adminAPI.getBaseURLs('subscribers')
    }


    static chats = {
        get: `admin/chats/`,
        with: `admin/chats/with/`,
        update: (id: number) => `admin/chats/${id}/`,
        messages: (id: number) => `admin/chats/${id}/messages/`,
        message_as_seen: (chatId: number, id: number) => `admin/chats/${chatId}/messages/${id}/set_as_seen/`
    }


    static home_layouts = {
        get: `admin/home_layouts/`, update: `admin/home_layouts/`, remove_image: `admin/home_layouts/remove_image/`,
    }
    static dashboard = {
        get: `admin/dashboard/`,
        revenues: `admin/dashboard/revenues/`,
        category_products: `admin/dashboard/category_products/`,

    }

    static zones = {
        ...adminAPI.getBaseURLs('zones')
    }

    static categories = {
        ...adminAPI.getBaseURLs('categories'), remove_image: (id: number) => `admin/categories/${id}/remove_image`
    }

    static coupons = {
        ...adminAPI.getBaseURLs('coupons')
    }

    static home_banners = {
        ...adminAPI.getBaseURLs('home_banners')
    }

    static sub_categories = {
        ...adminAPI.getBaseURLs('sub_categories'),
    }

    static customers = {
        ...adminAPI.getBaseURLs('customers'),
        remove_avatar: (id: number) => `admin/customers/${id}/remove_avatar`,
        wallet: (id: number) => `admin/customers/${id}/wallets`,
        add_money_to_wallet: (id: number) => `admin/customers/${id}/wallets/add_money`,
    }

    static shops = {
        ...adminAPI.getBaseURLs('shops'),
        delete: (id: number) => `admin/shops/${id}`,
        approve: (id: number) => `admin/shops/${id}/approve`,
        remove_logo: (id: number) => `admin/shops/${id}/remove_logo`,
        remove_cover_image: (id: number) => `admin/shops/${id}/remove_cover_image`,
        addons: (id: number) => `admin/shops/${id}/addons`,
        orders: (id: number) => `admin/shops/${id}/orders`,
        roles: (id: number) => `admin/shops/${id}/roles`,
        products: (id: number) => `admin/shops/${id}/products`,
        module: (id: number) => `admin/shops/${id}/module`,
        categories: (id: number) => `admin/shops/${id}/categories`,
        revenues: (id: number) => `admin/shops/${id}/revenues`,
        reviews: (id: number) => `admin/shops/${id}/reviews`,
        plan_histories: (id: number) => `admin/shops/${id}/plan_histories`,
        upgrade_plan: (id: number) => `admin/shops/${id}/upgrade_plan`,
    }

    static shop_times = {
        create: (shopId: number) => `admin/shops/${shopId}/times`,
        delete: (shopId: number, id: number) => `admin/shops/${shopId}/times/${id}`,

    }

    static delivery_boys = {
        ...adminAPI.getBaseURLs('delivery_boys'),
        remove_avatar: (id: number) => `admin/delivery_boys/${id}/remove_avatar`,
        remove_identity_image: (id: number) => `admin/delivery_boys/${id}/remove_identity_image`,
        approve: (id: number) => `admin/delivery_boys/${id}/approve`,
        delete: (id: number) => `admin/delivery_boys/${id}`,

    }
    static delivery_boy_reviews = {
        get: 'admin/delivery_boy_reviews/',
        delete: (id: number) => `admin/delivery_boy_reviews/${id}/`,
    }
    static delivery_boy_revenues = {
        get: 'admin/delivery_boy_revenues/',
    }
    static delivery_boy_payouts = {
        ...adminAPI.getBaseURLs('delivery_boy_payouts')
    }

    static addons = {
        ...adminAPI.getBaseURLs('addons'), remove_image: (id: number) => `admin/addons/${id}/remove_image`
    }
    static file_manager = {
        get: `admin/file_manager/`,
        upload: `admin/file_manager/`,

    }
    static shop_plans = {
        ...adminAPI.getBaseURLs('shop_plans')
    }


    static pos = {
        ...adminAPI.getBaseURLs('pos')
    }

    static revenues = {
        get: 'admin/revenues/',
    }

    static shop_revenues = {
        get: 'admin/shop_revenues/',
    }

   static reports = {
        report: 'admin/reports/report',
    }




    static shop_reviews = {
        get: 'admin/shop_reviews/', delete: (id: number) => `admin/shop_reviews/${id}/`,
    }


    static roles = {
        ...adminAPI.getBaseURLs('roles')
    }

    static shop_payouts = {
        ...adminAPI.getBaseURLs('shop_payouts')
    }

    static units = {
        ...adminAPI.getBaseURLs('units')
    }


    static business_settings = {
        get_data: 'admin/business_settings/',
        app_setting: 'admin/business_settings/app_setting/',
        payment_setting: 'admin/business_settings/payment_setting/',
        sms_setting: 'admin/business_settings/sms_setting/',
        other_setting: 'admin/business_settings/other_setting/',
        system_setting: 'admin/business_settings/system_setting/',
        info_setting: 'admin/business_settings/info_setting/',
        order_setting: 'admin/business_settings/order_setting/',
        mail_setting: 'admin/business_settings/mail_setting/',
    }

    static notifications = {
        get: `admin/notifications/`,
    }

    static manual_notifications = {
        ...adminAPI.getBaseURLs('manual_notifications')
    }

    static profile = {
        show: `admin/profile/`, update: `admin/profile/`, remove_avatar: `admin/profile/remove_avatar/`,
    }


    static getBaseURLs(model: string) {
        return {
            get: `admin/${model}/`,
            create: `admin/${model}/`,
            show: (id: number) => `admin/${model}/${id}/`,
            update: (id: number) => `admin/${model}/${id}/`
        }
    }

}

export class sellerAPI {
    static auth = {
        login: 'seller/login/',
        logout: 'seller/logout/',
        google_login: 'seller/google_login/',

    }

    static init = {
        get: `seller/init/`,
    }

    static notifications = {
        get: `seller/notifications/`,
    }

    static units = {
        get: `seller/units/`,
    }

    static dashboard = {
        get: `seller/dashboard/`,
        revenues: `seller/dashboard/revenues/`,
        selling_products: `seller/dashboard/selling_products/`,
        rated_products: `seller/dashboard/rated_products/`,

    }

    static categories = {
        ...sellerAPI.getBaseURLs('categories'),
    }

    static sub_categories = {
        ...sellerAPI.getBaseURLs('sub_categories'),
    }

    static products = {
        ...sellerAPI.getBaseURLs('products'),
        validate_import_data: `seller/products/validate_import_data`,
        import_data: `seller/products/import_data`,
        remove_availability: (id: number) => `seller/products/${id}/remove_availability`,
        reviews: (id: number) => `seller/products/${id}/reviews`,

    }

    static modules = {
        get: `seller/modules/`,
        my: `seller/modules/my/`,
    }

    static addons = {
        ...sellerAPI.getBaseURLs('addons'), remove_image: (id: number) => `seller/addons/${id}/remove_image`
    }

    static pos = {
        ...sellerAPI.getBaseURLs('pos')
    }

    static zones = {
        get: 'seller/zones/'
    }


    static customers = {
        ...sellerAPI.getBaseURLs('customers')
    }

    static product_options = {
        ...sellerAPI.getBaseURLs('product_options')
    }

    static delivery_boys = {
        ...sellerAPI.getBaseURLs('delivery_boys'),
        remove_avatar: (id: number) => `seller/delivery_boys/${id}/remove_avatar`,
        remove_identity_image: (id: number) => `seller/delivery_boys/${id}/remove_identity_image`,
    }

    static delivery_boy_reviews = {
        get: 'seller/delivery_boy_reviews/',
    }
    static delivery_boy_revenues = {
        get: 'seller/delivery_boy_revenues/',
    }
    static delivery_boy_payouts = {
        ...sellerAPI.getBaseURLs('delivery_boy_payouts')
    }

    static orders = {
        ...sellerAPI.getBaseURLs('orders'),
        cancel: (id: number) => `seller/orders/${id}/cancel/`,
        reject: (id: number) => `seller/orders/${id}/reject/`,
        accept: (id: number) => `seller/orders/${id}/accept/`,
        set_ready_at: (id: number) => `seller/orders/${id}/set_ready_at/`,
        ready: (id: number) => `seller/orders/${id}/ready/`,
        set_as_paid: (id: number) => `seller/orders/${id}/set_as_paid/`,
        assign_delivery: (id: number) => `seller/orders/${id}/assign_delivery/`,
        deliver: (id: number) => `seller/orders/${id}/deliver/`,
    }

    static product_images = {
        delete: (id: number) => `seller/product_images/${id}/`
    }

    static shops = {
        self_registration: `seller/self_registration/`,
        show: `seller/shops`,
        update: `seller/shops`,
        remove_logo: `seller/shops/remove_logo`,
        remove_cover_image: `seller/shops/remove_cover_image`,
    }



    static shop_times = {
        create: `seller/shops/times`, delete: (id: number) => `seller/shops/times/${id}`
    }

    static shop_reviews = {
        get: 'seller/shop_reviews/'
    }

    static sellers = {
        ...sellerAPI.getBaseURLs('sellers'),
        available: 'seller/sellers/available',
        remove_avatar: (id: number) => `seller/sellers/${id}/remove_avatar`
    }

    static revenues = {
        get: 'seller/shop_revenues/',
    }

    static shop_plans = {
        get: 'seller/shop_plans/',
        upgrade: 'seller/shop_plans/upgrade/',
    }

    static shop_plan_histories = {
        get: 'seller/shop_plan_histories/',
    }


    static payouts = {
        get: 'seller/shop_payouts/',
    }

    static roles = {
        ...sellerAPI.getBaseURLs('roles')
    }

    static reports = {
        report: 'seller/reports/report',
    }


    static chats = {
        get: `seller/chats/`,
        with: `seller/chats/with`,
        update: (id: number) => `seller/chats/${id}/`,
        messages: (id: number) => `seller/chats/${id}/messages/`,
        message_as_seen: (chatId: number, id: number) => `seller/chats/${chatId}/messages/${id}/set_as_seen/`
    }

    static profile = {
        show: `seller/profile/`, update: `seller/profile/`, remove_avatar: `seller/profile/remove_avatar/`,
    }

    static coupons = {
        ...sellerAPI.getBaseURLs('coupons')
    }


    static getBaseURLs(model: string) {
        return {
            get: `seller/${model}/`,
            create: `seller/${model}/`,
            show: (id: number) => `seller/${model}/${id}/`,
            update: (id: number) => `seller/${model}/${id}/`
        }
    }
}


export class customerAPI {
    static auth = {
        login: 'customer/login/',
        register: 'customer/register/',
        logout: 'customer/logout/',
        google_login: 'customer/google_login/',
    }

    static newsletter = {
        subscribe: 'customer/subscribe/',
    }

    static products = {
        ...customerAPI.getBaseURLs('products'),
        similar: (id: number) => `customer/products/${id}/similar/`,
        reviews: (id: number) => `customer/products/${id}/reviews/`,
    }

    static coupons = {
        get: `customer/coupons/`,
    }

    static notifications = {
        get: `customer/notifications/`,
    }

    static favorite_products = {
        ...customerAPI.getBaseURLs('customer_favorite_products'),
    }

    static favorite_shops = {
        ...customerAPI.getBaseURLs('customer_favorite_shops'),
    }

    static favorites = {
        ...customerAPI.getBaseURLs('customer_favorites'),
    }

    static profile = {
        show: `customer/profile/`,
        update: `customer/profile/`,
        send_verification_email: `customer/profile/send_verification_email`,
        remove_avatar: `customer/profile/remove_avatar/`,
    }


    static carts = {
        ...customerAPI.getBaseURLs('carts')
    }
    static cart_addons = {
        ...customerAPI.getBaseURLs('cart_addons')
    }

    static addresses = {
        ...customerAPI.getBaseURLs('customer_addresses'),
        selected: (id: number) => `customer/customer_addresses/${id}/selected/`,

    }

    static init = {
        get: `customer/init/`,

    }

    static wallets = {
        get: `customer/wallets/`
    }

    static loyalty_wallets = {
        get: `customer/loyalty_wallets/`,
        convert_points: `customer/loyalty_wallets/convert_points/`,
    }

    static business_setting = {
        get: `customer/global_data/`
    }

    static home_layouts = {
        get: `customer/home_layouts/`
    }
    static search = {
        get: `customer/search/`
    }

    static categories = {
        get: `customer/categories/`
    }

    static navigation = {
        get_route: `customer/navigation/get_route/`
    }

    static shops = {
        get: `customer/shops/`,
        show: (id: number) => `customer/shops/${id}/`,
        coupons: (id: number) => `customer/shops/${id}/coupons/`,
        carts: (id: number) => `customer/shops/${id}/carts/`,
        reviews: (id: number) => `customer/shops/${id}/reviews/`,
        myReview: (id: number) => `customer/shops/${id}/reviews/me/`,
    }


    static orders = {
        ...customerAPI.getBaseURLs('orders'),
        cancel: (id: number) => `customer/orders/${id}/cancel/`,
        invoice: (id: number) => `customer/orders/${id}/invoice/`,
        pay: (id: number) => `customer/orders/${id}/pay/`,
        resubmit: (id: number) => `customer/orders/${id}/resubmit/`,
        product_reviews: (id: number) => `customer/orders/${id}/product_reviews/`,
        delivery_boy_reviews: (id: number) => `customer/orders/${id}/delivery_boy_reviews/`,
    }

    static chats = {
        get: `customer/chats/`,
        with: `customer/chats/with/`,
        update: (id: number) => `customer/chats/${id}/`,
        messages: (id: number) => `customer/chats/${id}/messages/`,
        message_as_seen: (chatId: number, id: number) => `customer/chats/${chatId}/messages/${id}/set_as_seen/`
    }

    static payments = {
        data: `customer/payments/data/`,
        create_razorpay_checkout: `customer/payments/create_razorpay_checkout/`,
        create_cashfree_checkout: `customer/payments/create_cashfree_checkout/`,
        create_stripe_checkout: `customer/payments/create_stripe_checkout/`,
        create_paypal_checkout: `customer/payments/create_paypal_checkout/`,
        create_flutterwave_checkout: `customer/payments/create_flutterwave_checkout/`,
        create_paystack_checkout: `customer/payments/create_paystack_checkout/`,
    }

    static getBaseURLs(model: string) {
        return {
            get: `customer/${model}/`,
            create: `customer/${model}/`,
            show: (id: number) => `customer/${model}/${id}/`,
            update: (id: number) => `customer/${model}/${id}/`,
            delete: (id: number) => `customer/${model}/${id}/`
        }
    }
}


export class deliveryBoyAPI {
    static prefix = 'delivery_boy';

    static zones = {
        get: `${this.prefix}/zones/`
    }
    static auth = {
        register: `${this.prefix}/register/`
    };

}


export class installationAPI {
    static step_1 = {
        get: 'installations/step_1/',
    }

    static step_2 = {
        get: 'installations/step_2/', update_db: 'installations/step_2',
    }

    static step_3 = {
        get: 'installations/step_3/',
        migrate: 'installations/step_3/migrate',

    }

    static step_4 = {
        get: 'installations/step_4/',
        create: 'installations/step_4/',

    }
    static step_5 = {
        get: 'installations/step_5/',
        update: 'installations/step_5/',

    }

}
