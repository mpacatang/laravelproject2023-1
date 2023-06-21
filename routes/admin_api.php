<?php

use App\Http\Controllers\Api\v1\Admin\AddonController;
use App\Http\Controllers\Api\v1\Admin\AdminController;
use App\Http\Controllers\Api\v1\Admin\AdminRoleController;
use App\Http\Controllers\Api\v1\Admin\AuthController;
use App\Http\Controllers\Api\v1\Admin\BusinessSettingController;
use App\Http\Controllers\Api\v1\Admin\CategoryController;
use App\Http\Controllers\Api\v1\Admin\ChatController;
use App\Http\Controllers\Api\v1\Admin\ChatMessageController;
use App\Http\Controllers\Api\v1\Admin\CouponController;
use App\Http\Controllers\Api\v1\Admin\CustomerController;
use App\Http\Controllers\Api\v1\Admin\CustomerWalletController;
use App\Http\Controllers\Api\v1\Admin\DashboardController;
use App\Http\Controllers\Api\v1\Admin\DeliveryBoyController;
use App\Http\Controllers\Api\v1\Admin\DeliveryBoyPayoutController;
use App\Http\Controllers\Api\v1\Admin\DeliveryBoyRevenueController;
use App\Http\Controllers\Api\v1\Admin\DeliveryBoyReviewController;
use App\Http\Controllers\Api\v1\Admin\FileManagerController;
use App\Http\Controllers\Api\v1\Admin\HomeBannerController;
use App\Http\Controllers\Api\v1\Admin\HomeLayoutController;
use App\Http\Controllers\Api\v1\Admin\InitController;
use App\Http\Controllers\Api\v1\Admin\ModuleController;
use App\Http\Controllers\Api\v1\Admin\NotificationController;
use App\Http\Controllers\Api\v1\Admin\Notifications\ManualNotificationController;
use App\Http\Controllers\Api\v1\Admin\OrderController;
use App\Http\Controllers\Api\v1\Admin\POSController;
use App\Http\Controllers\Api\v1\Admin\ProductController;
use App\Http\Controllers\Api\v1\Admin\ProductImageController;
use App\Http\Controllers\Api\v1\Admin\ProductOptionController;
use App\Http\Controllers\Api\v1\Admin\ProductReviewController;
use App\Http\Controllers\Api\v1\Admin\ProfileController;
use App\Http\Controllers\Api\v1\Admin\ReportController;
use App\Http\Controllers\Api\v1\Admin\RevenueController;
use App\Http\Controllers\Api\v1\Admin\SellerController;
use App\Http\Controllers\Api\v1\Admin\ShopController;
use App\Http\Controllers\Api\v1\Admin\ShopPayoutController;
use App\Http\Controllers\Api\v1\Admin\ShopPlanController;
use App\Http\Controllers\Api\v1\Admin\ShopRevenueController;
use App\Http\Controllers\Api\v1\Admin\ShopReviewController;
use App\Http\Controllers\Api\v1\Admin\ShopTimeController;
use App\Http\Controllers\Api\v1\Admin\SubCategoryController;
use App\Http\Controllers\Api\v1\Admin\SubscriberController;
use App\Http\Controllers\Api\v1\Admin\UnitController;
use App\Http\Controllers\Api\v1\Admin\ZoneController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'v1/admin'], function () {
    //---------------- Auth --------------------//
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google_login', [AuthController::class, 'googleLogin']);


    Route::group(['middleware' => ['auth:admin-api']], function () {
        Route::get('init/', [InitController::class, 'index']);
        Route::get('notifications/', [NotificationController::class, 'index']);


        Route::group([], function () {
            Route::get('dashboard/', [DashboardController::class, 'index']);
            Route::get('dashboard/revenues/', [DashboardController::class, 'revenues']);
            Route::get('dashboard/category_products/', [DashboardController::class, 'categoryProducts']);
        });

        Route::group([], function () {
            Route::get('profile/', [ProfileController::class, 'show']);
            Route::patch('profile/', [ProfileController::class, 'update']);
            Route::patch('profile/remove_avatar/', [ProfileController::class, 'remove_avatar']);
            Route::post('logout', [AuthController::class, 'logout']);
        });


        Route::group(['middleware' => ['permission.admin:orders']], function () {
            Route::resource('orders', OrderController::class);
            Route::patch('orders/{id}/cancel', [OrderController::class, 'cancel']);
            Route::patch('orders/{id}/reject', [OrderController::class, 'reject']);
            Route::patch('orders/{id}/accept', [OrderController::class, 'accept']);
            Route::patch('orders/{id}/set_ready_at', [OrderController::class, 'setReadyAt']);
            Route::patch('orders/{id}/ready', [OrderController::class, 'ready']);
            Route::get('orders/{id}/assign_delivery', [OrderController::class, 'getDeliveryBoys']);
            Route::post('orders/{id}/assign_delivery', [OrderController::class, 'assignDeliveryBoy']);
            Route::patch('orders/{id}/deliver', [OrderController::class, 'deliver']);
            Route::patch('orders/{id}/set_as_paid', [OrderController::class, 'setAsPaid']);
        });

        Route::group(['middleware' => ['permission.admin:roles']], function () {
            Route::resource('roles', AdminRoleController::class);
        });

        Route::group(['middleware' => ['permission.admin:sellers']], function () {
            Route::resource('admins', AdminController::class);
            Route::delete('admins/{id}/remove_avatar', [AdminController::class, 'removeAvatar']);
        });


        Route::group(['middleware' => ['permission.admin:pos']], function () {
            Route::post('pos/', [POSController::class, 'store']);
        });

        Route::group(['middleware' => ['permission.admin:modules']], function () {
            Route::resource('modules', ModuleController::class)->only(['index', 'show', 'update']);
        });
        Route::group(['middleware' => ['permission.admin:zones']], function () {
            Route::resource('zones', ZoneController::class);
        });

        Route::group(['middleware' => ['permission.admin:home_banners']], function () {
            Route::resource('home_banners', HomeBannerController::class);
        });
        Route::group(['middleware' => ['permission.admin:units']], function () {
            Route::resource('units', UnitController::class)->only(['index', 'store', 'show', 'update']);
        });

        Route::group(['middleware' => ['permission.admin:home_layouts']], function () {
            Route::get('home_layouts/', [HomeLayoutController::class, 'index']);
            Route::patch('home_layouts/', [HomeLayoutController::class, 'update']);
        });


        Route::group(['middleware' => ['permission.admin:categories']], function () {
            Route::resource('categories', CategoryController::class);
            Route::patch('categories/{id}/remove_image', [CategoryController::class, 'removeImage']);
            Route::resource('sub_categories', SubCategoryController::class);
        });

        Route::group(['middleware' => ['permission.admin:coupons']], function () {
            Route::resource('coupons', CouponController::class);
        });

        Route::group(['middleware' => ['permission.admin:products']], function () {
            Route::resource('products', ProductController::class);
            Route::post('products/validate_import_data', [ProductController::class, 'validateImportData']);
            Route::post('products/import_data', [ProductController::class, 'importData']);


            Route::resource('product_options', ProductOptionController::class)->only(['store', 'update']);
            Route::patch('products/{id}/remove_availability', [ProductController::class, 'removeAvailability']);
            Route::get('products/{id}/reviews', [ProductController::class, 'reviews']);
            Route::resource('product_images', ProductImageController::class)->only(['destroy']);
            Route::resource('product_reviews', ProductReviewController::class)->only(['destroy']);
        });

        Route::group(['middleware' => ['permission.admin:addons']], function () {
            Route::resource('addons', AddonController::class)->only(['index', 'store', 'show', 'update']);
            Route::delete('addons/{id}/remove_image', [AddonController::class, 'removeImage']);
        });

        Route::group(['middleware' => ['permission.admin:reports']], function () {
            Route::get('reports/report', [ReportController::class, 'report']);
        });


        Route::group(['middleware' => ['permission.admin:sellers']], function () {
            Route::get('sellers/available_owners', [SellerController::class, 'availableOwners']);
            Route::resource('sellers', SellerController::class);
            Route::delete('sellers/{id}/remove_avatar', [SellerController::class, 'removeAvatar']);
        });


        Route::group(['middleware' => ['permission.admin:shops']], function () {
            Route::resource('shops', ShopController::class);
            Route::patch('shops/{id}/approve', [ShopController::class, 'approve']);
            Route::delete('shops/{id}/remove_logo', [ShopController::class, 'removeLogo']);
            Route::delete('shops/{id}/remove_cover_image', [ShopController::class, 'removeCoverImage']);
            Route::get('shops/{id}/addons', [ShopController::class, 'addons']);
            Route::get('shops/{id}/products', [ShopController::class, 'products']);
            Route::get('shops/{id}/categories', [ShopController::class, 'categories']);
            Route::get('shops/{id}/module', [ShopController::class, 'module']);
            Route::get('shops/{id}/roles', [ShopController::class, 'roles']);
            Route::get('shops/{id}/orders', [ShopController::class, 'orders']);
            Route::get('shops/{id}/revenues', [ShopController::class, 'revenues']);
            Route::get('shops/{id}/reviews', [ShopController::class, 'reviews']);
            Route::get('shops/{id}/plan_histories', [ShopController::class, 'planHistories']);
            Route::post('shops/{id}/upgrade_plan', [ShopController::class, 'upgradePlan']);

            Route::resource('shops/{shopId}/times', ShopTimeController::class)->only(['store', 'destroy']);

            Route::get('shop_revenues', [ShopRevenueController::class, 'index']);
            Route::resource('shop_payouts', ShopPayoutController::class)->only(['index', 'store']);
            Route::resource('shop_reviews', ShopReviewController::class)->only(['index', 'destroy']);
            Route::resource('shop_plans', ShopPlanController::class)->only(['index', 'store', 'show', 'update']);
        });


        Route::group(['middleware' => ['permission.admin:delivery_boys']], function () {
            Route::resource('delivery_boys', DeliveryBoyController::class);
            Route::patch('delivery_boys/{id}/approve', [DeliveryBoyController::class, 'approve']);
            Route::get('delivery_boy_revenues', [DeliveryBoyRevenueController::class, 'index']);
            Route::resource('delivery_boy_payouts', DeliveryBoyPayoutController::class)->only(['index', 'store']);
            Route::resource('delivery_boy_reviews', DeliveryBoyReviewController::class)->only(['index', 'destroy']);
            Route::delete('delivery_boys/{id}/remove_avatar', [DeliveryBoyController::class, 'removeAvatar']);
            Route::delete(
                'delivery_boys/{id}/remove_identity_image',
                [DeliveryBoyController::class, 'removeIdentityImage']
            );
        });

        Route::group(['middleware' => ['permission.admin:customers']], function () {
            Route::resource('customers', CustomerController::class)->only(['index', 'store', 'show', 'update']);
            Route::delete('customers/{id}/remove_avatar', [CustomerController::class, 'removeAvatar']);
            Route::get('customers/{id}/wallets', [CustomerWalletController::class, 'index']);
            Route::post('customers/{id}/wallets/add_money', [CustomerWalletController::class, 'addMoney']);
        });


        Route::group(['middleware' => ['permission.admin:revenues']], function () {
            Route::get('revenues', [RevenueController::class, 'index']);
        });

        Route::group(['middleware' => ['permission.admin:file_managers']], function () {
            Route::get('file_manager/', [FileManagerController::class, 'index']);
            Route::post('file_manager/', [FileManagerController::class, 'store']);
        });

        Route::group(['middleware' => ['permission.admin:subscribers']], function () {
            Route::get('subscribers', [SubscriberController::class, 'index']);
        });


        Route::group(['prefix' => 'business_settings', 'middleware' => ['permission.admin:business_settings']],
            function () {
                Route::get('', [BusinessSettingController::class, 'index']);

                Route::post('app_setting', [BusinessSettingController::class, 'updateAppSetting']);
                Route::post('info_setting', [BusinessSettingController::class, 'updateInfoSetting']);
                Route::post('order_setting', [BusinessSettingController::class, 'updateOrderSetting']);
                Route::post('payment_setting', [BusinessSettingController::class, 'updatePaymentSetting']);
                Route::post('mail_setting', [BusinessSettingController::class, 'updateMailSetting']);
                Route::post('sms_setting', [BusinessSettingController::class, 'updateSmsSetting']);
                Route::post('system_setting', [BusinessSettingController::class, 'updateSystemSetting']);
                Route::post('other_setting', [BusinessSettingController::class, 'updateOtherSetting']);
            });

        Route::group(['middleware' => ['permission.admin:notifications']], function () {
            Route::resource('manual_notifications', ManualNotificationController::class)->only(
                ['index', 'store', 'update', 'show']
            );
        });

        //Chats
        Route::get('chats/', [ChatController::class, 'index']);
        Route::get('chats/with/', [ChatController::class, 'with']);

        Route::patch('chats/{chatId}/', [ChatController::class, 'update']);
        Route::get('chats/{chatId}/messages', [ChatMessageController::class, 'index']);
        Route::post('chats/{chatId}/messages', [ChatMessageController::class, 'create']);

        Route::patch('chats/{chatId}/messages/{id}/set_as_seen', [ChatMessageController::class, 'setAsSeen']);
    });
});

