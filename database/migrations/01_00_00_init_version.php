<?php

use App\Models\Addon;
use App\Models\AdminRole;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerLoyaltyWallet;
use App\Models\CustomerWallet;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyPayout;
use App\Models\HomeLayout;
use App\Models\Module;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use App\Models\SellerRole;
use App\Models\Shop;
use App\Models\ShopPayout;
use App\Models\ShopPlan;
use App\Models\ShopTime;
use App\Models\ShortMessage;
use App\Models\SubCategory;
use App\Models\Unit;
use App\Models\Zone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */


    public function tokenLogs(): void
    {
        Schema::create('token_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('access_token')->unique();
            $table->string('fcm_token')->nullable();
            $table->timestamp('login_at')->default(now());
            $table->timestamp('logout_at')->nullable();
            $table->timestamps();

        });

    }

    public function businessSettings(): void
    {

        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();

            $table->timestamps();
        });
    }


    public function modules()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('type')->unique();
            $table->string('title')->unique();
            $table->timestamps();
        });
    }

    public function zones()
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('name')->unique();
            $table->polygon('coordinates');
            $table->timestamps();
        });
    }


    public function customers()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);

            $table->string('first_name');
            $table->string('last_name');

            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('mobile_number')->nullable()->unique();
            $table->timestamp('mobile_number_verified_at')->nullable();

            $table->string('referral', 10);
            $table->string('from_referral', 10)->nullable();
            $table->string('avatar')->nullable();

            $table->string('password');
            $table->timestamps();
        });
    }

    public function customerWallets()
    {
        Schema::create('customer_wallets', function (Blueprint $table) {
            $table->id();

            $table->double('balance')->default(0);


            $table->foreignIdFor(Customer::class)->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function customerLoyaltyWallets()
    {
        Schema::create('customer_loyalty_wallets', function (Blueprint $table) {
            $table->id();

            $table->double('point')->default(0);


            $table->foreignIdFor(Customer::class)->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function adminRoles(): void
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);

            $table->string('title');
            $table->json('permissions');

            $table->timestamps();
        });
    }


    public function admins()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->boolean('is_super')->default(false);

            $table->string('first_name');
            $table->string('last_name');

            $table->string('email')->unique();
            $table->string('mobile_number')->unique();
            $table->string('password');

            $table->string('avatar')->nullable();
            $table->foreignIdFor(AdminRole::class, 'role_id')->nullable()->constrained('admin_roles')->cascadeOnDelete();

            $table->timestamps();
        });
    }


    public function shopPlans()
    {

        Schema::create('shop_plans', function (Blueprint $table) {
            $charges_types = array('percent', 'amount');
            $table->id();
            $table->boolean('active')->default(true);

            $table->string('title');
            $table->string('sub_title');
            $table->double('price');

            $table->float('admin_commission')->default(0);
            $table->enum('admin_commission_type', $charges_types)->default($charges_types[0]);

            $table->integer('products');
            $table->timestamps();
        });
    }

    public function shops(): void
    {


        Schema::create('shops', function (Blueprint $table) {
            $tax_types = array('percent', 'amount');
            $delivery_time_types = array(Shop::$minutes, Shop::$hours, Shop::$days);

            $table->id();
            $table->boolean('archived')->default(false);
            $table->boolean('active')->default(true);
            $table->boolean('approved')->default(false);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile_number')->unique();
            $table->text('description')->nullable();
            $table->boolean('open')->default(false);

            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('license_number')->nullable();


            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->integer('pincode');
            $table->double('latitude');
            $table->double('longitude');


            $table->double('packaging_charge')->default(0);
            $table->double('tax')->default(0);
            $table->enum('tax_type', $tax_types)->default($tax_types[0]);
            $table->double('admin_commission')->default(0);
            $table->enum('admin_commission_type', $tax_types)->default($tax_types[0]);

            $table->double('rating')->default(0);
            $table->bigInteger('ratings_total')->default(0);
            $table->bigInteger('ratings_count')->default(0);


            $table->double('min_order_amount')->default(0);
            $table->boolean('take_away')->default(false);

            $table->integer('min_delivery_time')->nullable();
            $table->integer('max_delivery_time')->nullable();
            $table->enum('delivery_time_type', $delivery_time_types)->nullable();

            $table->boolean('open_for_delivery')->default(false);
            $table->boolean('self_delivery')->default(false);
            $table->bigInteger('delivery_range')->default(0)->nullable();
            $table->double('minimum_delivery_charge')->default(0)->nullable();
            $table->double('delivery_charge_multiplier')->default(0)->nullable();


            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Zone::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ShopPlan::class)->constrained()->cascadeOnDelete();


            $table->unique(['latitude', 'longitude']);

            $table->timestamps();
        });
    }

    public function shopPlanHistories()
    {
        Schema::create('shop_plan_histories', function (Blueprint $table) {
            $table->id();


            $table->double('price');

            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();

            $table->mediumInteger('duration')->nullable();

            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ShopPlan::class)->constrained()->cascadeOnDelete();


            $table->timestamps();
        });
    }

    public function shopTimes(): void
    {

        Schema::create('shop_times', function (Blueprint $table) {
            $table->id();
            $table->enum('day', ShopTime::$days);
            $table->time('open_at');
            $table->time('close_at');


            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();


            $table->timestamps();
        });
    }

    public function deliveryBoys(): void
    {


        Schema::create('delivery_boys', function (Blueprint $table) {
            $table->id();

            $table->boolean('active')->default(true);
            $table->boolean('approved')->default(false);
            $table->boolean('archived')->default(false);

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile_number')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();


            $table->string('identity_type');
            $table->string('identity_number');
            $table->string('identity_image')->nullable();
            $table->string('vehicle_number');

            $table->boolean('active_for_delivery')->default(true);
            $table->boolean('on_delivery')->default(false);
            $table->boolean('salary_based')->default(false);


            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();

            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();

            $table->bigInteger('rating')->default(0);
            $table->bigInteger('ratings_total')->default(0);
            $table->bigInteger('ratings_count')->default(0);


            $table->foreignIdFor(Zone::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Shop::class)->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }


    public function sellerRoles(): void
    {
        Schema::create('seller_roles', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);

            $table->string('title');
            $table->json('permissions');

            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();


            $table->timestamps();
        });
    }

    public function sellers()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();

            $table->boolean('active')->default(true);

            $table->string('first_name');
            $table->string('last_name');

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile_number')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();


            $table->boolean('is_owner')->default(false);
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();


            $table->foreignIdFor(SellerRole::class, 'role_id')->nullable()->constrained('seller_roles')->nullOnDelete();
            $table->foreignIdFor(Shop::class)->nullable()->constrained()->nullOnDelete();


            $table->timestamps();


        });
    }

    public function units()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('type');
            $table->string('title');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function categories(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();


            $table->timestamps();
        });
    }

    public function subCategories()
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('name');
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }


    public function productVariants()
    {

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id()->from(100001);

            $table->timestamps();
        });
    }

    public function products(): void
    {

        $food_types = array(Product::$veg_type, Product::$non_veg_type, Product::$vegan_type,);


        //TODO: ----  Need to add product meta (Key - Value) - (Battery - 4500maH)
        Schema::create('products', function (Blueprint $table) use ($food_types) {
            $table->id()->from(100001);

            $table->boolean('active')->default(true);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug');
            $table->string('unit_title')->nullable();
            $table->enum('food_type', $food_types)->nullable();
            $table->boolean('need_prescription')->default(false);


            $table->time('available_from')->nullable();
            $table->time('available_to')->nullable();
            $table->double('rating')->default(0);
            $table->bigInteger('ratings_total')->default(0);
            $table->bigInteger('ratings_count')->default(0);
            $table->bigInteger('selling_count')->default(0);

            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SubCategory::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProductVariant::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Unit::class)->nullable()->constrained()->nullOnDelete();


            $table->timestamps();
        });
    }

    public function productOptions()
    {

        Schema::create('product_options', function (Blueprint $table) {
            $discount_types = array('percent', 'amount');
            $table->id()->from(100001);
            $table->string('option_value')->nullable();

            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->integer('stock');
            $table->float('price');

            $table->float('discount')->default(0);
            $table->enum('discount_type', $discount_types)->default($discount_types[0]);
            $table->float('total_discount')->default(0);
            $table->float('calculated_price')->default(0);

            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();


            $table->timestamps();
        });
    }

    public function productImages()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function addons(): void
    {

        Schema::create('addons', function (Blueprint $table) {
            $table->id()->from(100001);

            $table->string('name');

            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->float('price');
            $table->boolean('active')->default(true);

            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();


            $table->timestamps();
        });
    }

    public function productAddons(): void
    {

        Schema::create('product_addons', function (Blueprint $table) {
            $table->id()->from(100001);

            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Addon::class)->constrained()->cascadeOnDelete();

            $table->unique(['product_id', 'addon_id']);

            $table->timestamps();
        });
    }

    public function coupons()
    {


        Schema::create('coupons', function (Blueprint $table) {
            $discount_types = array('percent', 'amount');
            $table->id();
            $table->boolean('active')->default(true);
            $table->string('code')->unique();
            $table->longText('description')->nullable();
            $table->dateTime('started_at')->default(now());
            $table->dateTime('expired_at');


            $table->float('discount')->nullable();
            $table->enum('discount_type', $discount_types)->default($discount_types[0]);
            $table->float('min_order')->nullable();
            $table->float('max_discount')->nullable();
            $table->boolean('delivery_free')->default(false);
            $table->integer('limit')->default(1);
            $table->boolean('first_order')->default(false);

            $table->foreignIdFor(Shop::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Module::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Zone::class)->nullable()->constrained()->nullOnDelete();


            $table->timestamps();
        });
    }

    public function customerFavoriteProducts()
    {
        Schema::create('customer_favorite_products', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();

            $table->unique(['customer_id', 'product_id']);

            $table->timestamps();
        });
    }

    public function customerFavoriteShops()
    {
        Schema::create('customer_favorite_shops', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();

            $table->unique(['customer_id', 'shop_id']);

            $table->timestamps();
        });
    }

    public function customerAddresses(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);
            $table->boolean('selected')->default(false);
            $table->string('type');
            $table->string('address');
            $table->string('landmark')->nullable();
            $table->string('city');
            $table->integer('pincode');
            $table->double('longitude');
            $table->double('latitude');

            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function carts(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity')->default(1);

            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProductOption::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function cartAddons(): void
    {
        Schema::create('cart_addons', function (Blueprint $table) {
            $table->id();

            $table->integer('quantity')->default(1);

            $table->foreignIdFor(Cart::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Addon::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }


    public function orders()
    {
        $order_types = array(Order::$delivery_type, Order::$self_pickup_type, Order::$pos,);


//        TODO: ----- Add more validation
        Schema::create('orders', function (Blueprint $table) use ($order_types) {
            $table->id()->from(100001);


            $table->integer('otp');
            $table->string('invoice_otp');
            $table->boolean('complete')->default(false);
            $table->timestamp('ready_at')->nullable();
            $table->string('notes')->nullable();

            $table->enum('order_type', $order_types)->default($order_types[0]);

            $table->double('order_amount');
            $table->double('packaging_charge')->default(0);
            $table->double('tax')->default(0);
            $table->double('order_commission')->default(0);

            $table->double('delivery_charge')->default(0);
            $table->double('minimum_delivery_charge')->default(0);
            $table->double('delivery_charge_multiplier')->default(0);
            $table->mediumInteger('delivery_distance')->default(0);
            $table->double('delivery_commission')->default(0);

            $table->double('coupon_discount')->default(0);

            $table->double('payment_charge')->default(0);

            $table->double('paid_amount')->default(0);
            $table->double('change_amount')->default(0);

            $table->double('total');


            $table->double('admin_revenue_amount')->default(0);
            $table->double('delivery_boy_revenue_amount')->default(0);
            $table->double('shop_revenue_amount')->default(0);


            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CustomerAddress::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Coupon::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(DeliveryBoy::class, 'assign_delivery_boy_id')->nullable()->constrained('delivery_boys')->nullOnDelete();
            $table->foreignIdFor(DeliveryBoy::class)->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    public function orderItems()
    {


        Schema::create('order_items', function (Blueprint $table) {
            $discount_types = array('percent', 'amount');
            $table->id();
            $table->float('price');
            $table->float('calculated_price');
            $table->float('discount')->default(0);
            $table->enum('discount_type', $discount_types)->default($discount_types[0]);
            $table->integer('quantity')->default(1);

            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProductOption::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function orderItemAddons()
    {
        Schema::create('order_item_addons', function (Blueprint $table) {
            $table->id();
            $table->float('price');
            $table->integer('quantity')->default(1);

            $table->foreignIdFor(OrderItem::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Addon::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function orderPayments()
    {
        $payment_types = array(OrderPayment::$cash_on_delivery_type, OrderPayment::$wallet, OrderPayment::$cash, OrderPayment::$card,);


        $payment_statuses = array(OrderPayment::$unpaid_status, OrderPayment::$paid_status);


        Schema::create('order_payments', function (Blueprint $table) use ($payment_statuses, $payment_types) {
            $table->id();
            $table->enum('payment_type', $payment_types)->default($payment_types[0]);

            $table->enum('payment_status', $payment_statuses)->default($payment_statuses[0]);
            $table->float('total_payment');
            $table->float('payment')->default(0);

            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function orderMedias()
    {


        Schema::create('order_medias', function (Blueprint $table) {
            $table->id();
            $table->string('media')->nullable();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function orderStatuses()
    {

        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default(OrderStatus::$place_order_status);
            $table->string('description')->nullable();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function customerWalletTransactions()
    {


        Schema::create('customer_wallet_transactions', function (Blueprint $table) {
            $table->id();

            $table->boolean('added')->default(false);
            $table->double('amount');
            $table->string('payment_id')->nullable();


            $table->string('payment_method')->nullable();

            $table->foreignIdFor(CustomerWallet::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Order::class)->nullable()->constrained()->nullOnDelete();


            $table->timestamps();
        });
    }

    public function customerLoyaltyTransactions()
    {
        Schema::create('customer_loyalty_transactions', function (Blueprint $table) {
            $table->id();

            $table->boolean('added')->default(false);
            $table->double('point');

            $table->foreignIdFor(CustomerLoyaltyWallet::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Order::class)->nullable()->constrained()->nullOnDelete();


            $table->timestamps();
        });
    }

    public function productReviews()
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->smallInteger('rating');
            $table->string('review')->nullable();

            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ProductOption::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();

            $table->unique(['product_option_id', 'customer_id', 'order_id']);

            $table->timestamps();
        });
    }


    public function deliveryBoyReviews()
    {
        Schema::create('delivery_boy_reviews', function (Blueprint $table) {
            $table->id();

            $table->smallInteger('rating');
            $table->string('review')->nullable();

            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(DeliveryBoy::class)->constrained()->cascadeOnDelete();

            $table->unique(['delivery_boy_id', 'customer_id', 'order_id']);

            $table->timestamps();
        });
    }

    public function shopReviews()
    {
        Schema::create('shop_reviews', function (Blueprint $table) {
            $table->id();

            $table->smallInteger('rating');
            $table->string('review')->nullable();

            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();

            $table->unique(['shop_id', 'customer_id']);

            $table->timestamps();
        });
    }


    public function shopPayouts()
    {
        Schema::create('shop_payouts', function (Blueprint $table) {
            $table->id();
            $table->double('pay_to_shop')->default(0);
            $table->double('take_from_shop')->default(0);
            $table->dateTime('till_date');

            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function shopRevenues()
    {
        Schema::create('shop_revenues', function (Blueprint $table) {
            $table->id();

            $table->double('order_amount')->default(0);
            $table->double('tax')->default(0);
            $table->double('packaging_charge')->default(0);
            $table->double('delivery_charge')->default(0);


            $table->double('revenue');

            $table->double('pay_to_admin')->default(0);
            $table->double('take_from_admin')->default(0);
            $table->double('pay_to_delivery_boy')->default(0);
            $table->double('take_from_delivery_boy')->default(0);
            $table->double('collected_payment_from_customer')->default(0);

            $table->foreignIdFor(Shop::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ShopPayout::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(DeliveryBoy::class)->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

        });
    }

    public function deliveryBoyPayouts(): void
    {
        Schema::create('delivery_boy_payouts', function (Blueprint $table) {
            $table->id();
            $table->double('pay_to_admin')->default(0);
            $table->double('take_from_admin')->default(0);
            $table->double('pay_to_shop')->default(0);
            $table->double('take_from_shop')->default(0);
            $table->dateTime('till_date');

            $table->foreignIdFor(DeliveryBoy::class)->constrained()->cascadeOnDelete();


            $table->timestamps();
        });
    }

    public function deliveryBoyRevenues()
    {
        Schema::create('delivery_boy_revenues', function (Blueprint $table) {
            $table->id();

            $table->double('revenue')->default(0);

            $table->double('pay_to_admin')->default(0);
            $table->double('take_from_admin')->default(0);
            $table->double('pay_to_shop')->default(0);
            $table->double('take_from_shop')->default(0);
            $table->double('collected_payment_from_customer')->default(0);

            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(DeliveryBoy::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(DeliveryBoyPayout::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Shop::class)->nullable()->constrained()->nullOnDelete();


            $table->timestamps();
        });
    }

    public function adminRevenues()
    {
        Schema::create('admin_revenues', function (Blueprint $table) {
            $table->id();

            $table->double('delivery_charge')->default(0);
            $table->double('delivery_commission')->default(0);
            $table->double('order_commission')->default(0);
            $table->double('coupon_discount')->default(0);
            $table->double('payment_charge')->default(0);

            $table->double('revenue');

            $table->double('pay_to_shop')->default(0);
            $table->double('take_from_shop')->default(0);
            $table->double('pay_to_delivery_boy')->default(0);
            $table->double('take_from_delivery_boy')->default(0);
            $table->double('collected_payment_from_customer')->default(0);

            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Module::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Shop::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(DeliveryBoy::class)->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    public function homeBanners()
    {
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);

            $table->string('image');

            $table->foreignIdFor(Product::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Shop::class)->nullable()->constrained()->nullOnDelete();


            $table->timestamps();
        });
    }

    public function homeLayouts()
    {
        $types = array(
            HomeLayout::$featured_shops_type,
            HomeLayout::$featured_products_type,
            HomeLayout::$popular_products_type,
            HomeLayout::$latest_products_type,
            HomeLayout::$banner_type,
            HomeLayout::$other_type,);

        Schema::create('home_layouts', function (Blueprint $table) use ($types) {
            $table->id();
            $table->boolean('active')->default(false);
            $table->enum('type', $types);
            $table->json('item_ids')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->unique(['type']);
        });
    }

    public function navigationCaches(): void
    {
        Schema::create('navigation_caches', function (Blueprint $table) {
            $table->id();
            $table->double('lat_1');
            $table->double('lng_1');
            $table->double('lat_2');
            $table->double('lng_2');
            $table->mediumText('data');
            $table->timestamps();
        });
    }

    public function subscribers(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email');

            $table->timestamps();

            $table->unique(['email']);
        });
    }


    public function emailVerifications(): void
    {
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('verifiable');
            $table->string('email');
            $table->string('token');
            $table->timestamps();
        });
    }

    public function otpVerifications(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('verifiable');
            $table->string('mobile_number')->nullable();
            $table->string('email')->nullable();
            $table->string('otp');
            $table->timestamps();
        });
    }

    public function failedJobs(): void
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }


    public function jobs(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    public function notifications(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable');
            $table->string('title');
            $table->string('body');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }


    public function manual_notifications(): void
    {
        Schema::create('manual_notifications', function (Blueprint $table) {
            $table->id();
            $table->boolean('all_customers')->default(false);
            $table->boolean('all_sellers')->default(false);
            $table->boolean('all_delivery_boys')->default(false);
            $table->string('title');
            $table->string('body');
            $table->json('data')->nullable();
            $table->dateTime('schedule_at')->nullable();
            $table->timestamps();
        });
    }

    public function shortMessages(): void
    {
        Schema::create('short_messages', function (Blueprint $table) {
            $table->id();
            $table->morphs('messageable');
            $table->string('message');
            $table->string('type')->default(ShortMessage::$other_type);
            $table->dateTime('schedule_at')->nullable();
            $table->timestamps();
        });
    }


    public function up(): void
    {
        $this->tokenLogs();
        $this->businessSettings();
        $this->modules();
        $this->zones();
        $this->customers();
        $this->customerWallets();
        $this->customerLoyaltyWallets();

        $this->adminRoles();
        $this->admins();
        $this->shopPlans();
        $this->shops();
        $this->shopPlanHistories();
        $this->shopTimes();
        $this->deliveryBoys();
        $this->sellerRoles();
        $this->sellers();
        $this->units();
        $this->categories();
        $this->subCategories();
        $this->productVariants();
        $this->products();
        $this->productOptions();
        $this->productImages();
        $this->addons();
        $this->productAddons();
        $this->coupons();

        $this->customerFavoriteProducts();
        $this->customerFavoriteShops();
        $this->customerAddresses();

        $this->carts();
        $this->cartAddons();
        $this->orders();
        $this->orderItems();
        $this->orderItemAddons();
        $this->orderPayments();
        $this->orderMedias();
        $this->orderStatuses();

        $this->customerWalletTransactions();
        $this->customerLoyaltyTransactions();
        $this->productReviews();
        $this->deliveryBoyReviews();
        $this->shopReviews();

        $this->shopPayouts();
        $this->shopRevenues();
        $this->deliveryBoyPayouts();
        $this->deliveryBoyRevenues();
        $this->adminRevenues();
        $this->homeBanners();
        $this->homeLayouts();
        $this->navigationCaches();

        $this->subscribers();

        $this->failedJobs();
        $this->emailVerifications();
        $this->otpVerifications();
        $this->jobs();
        $this->notifications();
        $this->manual_notifications();
        $this->shortMessages();

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('token_logs');
        Schema::dropIfExists('business_settings');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('zones');

        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_wallets');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('shop_plans');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('shop_plan_histories');
        Schema::dropIfExists('shop_times');
        Schema::dropIfExists('delivery_boys');
        Schema::dropIfExists('seller_roles');
        Schema::dropIfExists('sellers');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sub_categories');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('addons');
        Schema::dropIfExists('product_addons');
        Schema::dropIfExists('coupons');

        Schema::dropIfExists('customer_favorite_products');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('cart_addons');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_item_addons');
        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('order_medias');
        Schema::dropIfExists('order_statuses');
        Schema::dropIfExists('customer_wallet_transactions');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('delivery_boy_reviews');
        Schema::dropIfExists('shop_reviews');

        Schema::dropIfExists('shop_payouts');
        Schema::dropIfExists('shop_revenues');
        Schema::dropIfExists('delivery_boy_payouts');
        Schema::dropIfExists('delivery_boy_revenues');
        Schema::dropIfExists('admin_revenues');
        Schema::dropIfExists('home_banners');
        Schema::dropIfExists('home_layouts');
        Schema::dropIfExists('navigation_caches');


    }
};
