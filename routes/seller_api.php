<?php

use App\Http\Controllers\Api\v1\Seller\AddonController;
use App\Http\Controllers\Api\v1\Seller\AuthController;
use App\Http\Controllers\Api\v1\Seller\CategoryController;
use App\Http\Controllers\Api\v1\Seller\ChatController;
use App\Http\Controllers\Api\v1\Seller\ChatMessageController;
use App\Http\Controllers\Api\v1\Seller\CouponController;
use App\Http\Controllers\Api\v1\Seller\CustomerController;
use App\Http\Controllers\Api\v1\Seller\DashboardController;
use App\Http\Controllers\Api\v1\Seller\DeliveryBoyController;
use App\Http\Controllers\Api\v1\Seller\DeliveryBoyPayoutController;
use App\Http\Controllers\Api\v1\Seller\DeliveryBoyRevenueController;
use App\Http\Controllers\Api\v1\Seller\DeliveryBoyReviewController;
use App\Http\Controllers\Api\v1\Seller\InitController;
use App\Http\Controllers\Api\v1\Seller\ModuleController;
use App\Http\Controllers\Api\v1\Seller\NotificationController;
use App\Http\Controllers\Api\v1\Seller\OrderController;
use App\Http\Controllers\Api\v1\Seller\POSController;
use App\Http\Controllers\Api\v1\Seller\ProductController;
use App\Http\Controllers\Api\v1\Seller\ProductImageController;
use App\Http\Controllers\Api\v1\Seller\ProductOptionController;
use App\Http\Controllers\Api\v1\Seller\ProfileController;
use App\Http\Controllers\Api\v1\Seller\ReportController;
use App\Http\Controllers\Api\v1\Seller\SellerController;
use App\Http\Controllers\Api\v1\Seller\SellerRoleController;
use App\Http\Controllers\Api\v1\Seller\ShopController;
use App\Http\Controllers\Api\v1\Seller\ShopPlanController;
use App\Http\Controllers\Api\v1\Seller\ShopPlanHistoryController;
use App\Http\Controllers\Api\v1\Seller\ShopReviewController;
use App\Http\Controllers\Api\v1\Seller\ShopTimeController;
use App\Http\Controllers\Api\v1\Seller\SubCategoryController;
use App\Http\Controllers\Api\v1\Seller\UnitController;
use App\Http\Controllers\Api\v1\Seller\ZoneController;
use App\Http\Middleware\EnsureSellerHasShop;
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


Route::group(['prefix' => 'v1/seller'], function () {

    //---------------- Public APIs ======================//
    Route::get('modules/', [ModuleController::class, 'index']);
    Route::get('zones/', [ZoneController::class, 'index']);
    Route::get('shop_plans/', [ShopPlanController::class, 'index']);
    Route::post('self_registration/', [ShopController::class, 'store']);


    //---------------- Auth --------------------//
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google_login', [AuthController::class, 'googleLogin']);
    Route::post('send_otp', [AuthController::class, 'sendOtp']);
    Route::post('login_via_otp', [AuthController::class, 'loginViaOtp']);


    Route::group(['middleware' => ['guest_auth:seller-api']], function () {
        Route::get('init/', [InitController::class, 'index']);
    });


    Route::group(['middleware' => ['auth:seller-api']], function () {

        Route::post('logout', [AuthController::class, 'logout']);

        Route::middleware([EnsureSellerHasShop::class])->group(function () {
            Route::get('notifications/', [NotificationController::class, 'index']);

            Route::get('profile/', [ProfileController::class, 'show']);
            Route::patch('profile/', [ProfileController::class, 'update']);
            Route::post('profile/delete_account/', [ProfileController::class, 'deleteAccount']);
            Route::patch('profile/remove_avatar/', [ProfileController::class, 'removeAvatar']);

            Route::get('modules/my/', [ModuleController::class, 'my']);


            Route::group(['middleware' => ['permission.seller:dashboard']], function () {
                Route::get('dashboard/', [DashboardController::class, 'index']);
                Route::get('dashboard/revenues/', [DashboardController::class, 'revenues']);
                Route::get('dashboard/rated_products/', [DashboardController::class, 'ratedProducts']);
                Route::get('dashboard/selling_products/', [DashboardController::class, 'sellingProduct']);

            });

            Route::group(['middleware' => ['permission.seller:orders']], function () {
                Route::resource('orders', OrderController::class)->only(['index', 'show']);
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


            Route::group(['middleware' => ['permission.seller:shops']], function () {
                Route::get('shops/', [ShopController::class, 'show']);
                Route::patch('shops/', [ShopController::class, 'update']);
                Route::delete('shops/remove_logo', [ShopController::class, 'removeLogo']);
                Route::delete('shops/remove_cover_image', [ShopController::class, 'removeCoverImage']);
                Route::resource('shops/times', ShopTimeController::class)->only(['index', 'store', 'destroy']);

                Route::post('shop_plans/upgrade/', [ShopPlanController::class, 'upgrade']);
                Route::resource('shop_plan_histories', ShopPlanHistoryController::class)->only(['index']);

            });


            Route::group(['middleware' => ['permission.seller:reviews']], function () {
                Route::resource('shop_reviews', ShopReviewController::class)->only(['index']);

            });


            Route::group(['middleware' => ['permission.seller:products']], function () {
                Route::get('units/', [UnitController::class, 'index']);
                Route::resource('categories', CategoryController::class)->only(['index']);
                Route::resource('sub_categories', SubCategoryController::class)->only(['index']);

                Route::resource('products', ProductController::class);
                Route::post('products/validate_import_data', [ProductController::class, 'validateImportData']);
                Route::post('products/import_data', [ProductController::class, 'importData']);

                Route::patch('products/{id}/remove_availability', [ProductController::class, 'removeAvailability']);

                Route::resource('product_images', ProductImageController::class)->only(['destroy']);
                Route::resource('product_options', ProductOptionController::class)->only(['store', 'update']);

                Route::get('products/{id}/reviews', [ProductController::class, 'reviews']);

            });

            Route::group(['middleware' => ['permission.seller:coupons']], function () {
                Route::resource('coupons', CouponController::class)->only(['index']);
            });

            Route::group(['middleware' => ['permission.seller:addons']], function () {
                Route::resource('addons', AddonController::class);
                Route::delete('addons/{id}/remove_image', [AddonController::class, 'removeImage']);
            });


            Route::group(['middleware' => ['permission.seller:sellers']], function () {
                Route::get('sellers/available', [SellerController::class, 'available']);
                Route::resource('sellers', SellerController::class);
                Route::delete('sellers/{id}/remove_avatar', [SellerController::class, 'removeAvatar']);
            });

            Route::group(['middleware' => ['permission.seller:pos']], function () {
                Route::post('pos/', [POSController::class, 'store']);
                Route::resource('customers/', CustomerController::class)->only(['index', 'store']);
            });


            Route::group(['middleware' => ['permission.seller:delivery_boys']], function () {
                Route::resource('delivery_boys', DeliveryBoyController::class)->only(['index', 'store', 'show', 'update']);
                Route::get('delivery_boy_revenues', [DeliveryBoyRevenueController::class, 'index']);
                Route::resource('delivery_boy_payouts', DeliveryBoyPayoutController::class)->only(['index', 'store']);
                Route::resource('delivery_boy_reviews', DeliveryBoyReviewController::class)->only(['index']);
                Route::delete('delivery_boys/{id}/remove_avatar', [DeliveryBoyController::class, 'removeAvatar']);
                Route::delete('delivery_boys/{id}/remove_identity_image', [DeliveryBoyController::class, 'removeIdentityImage']);
            });

            Route::group(['middleware' => ['permission.seller:revenues']], function () {
                Route::get('shop_revenues', [DeliveryBoyRevenueController::class, 'index']);
                Route::get('shop_payouts', [DeliveryBoyPayoutController::class, 'index']);
            });

            Route::group(['middleware' => ['permission.seller:reports']], function () {
                Route::get('reports/report', [ReportController::class, 'report']);
            });


            Route::group(['middleware' => ['permission.seller:roles']], function () {
                Route::resource('roles', SellerRoleController::class);
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
});

