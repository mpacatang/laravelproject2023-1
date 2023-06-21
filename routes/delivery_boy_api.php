<?php

use App\Http\Controllers\Api\v1\DeliveryBoy\AuthController;
use App\Http\Controllers\Api\v1\DeliveryBoy\ChatController;
use App\Http\Controllers\Api\v1\DeliveryBoy\ChatMessageController;
use App\Http\Controllers\Api\v1\DeliveryBoy\DashboardController;
use App\Http\Controllers\Api\v1\DeliveryBoy\DeliveryBoyPayoutController;
use App\Http\Controllers\Api\v1\DeliveryBoy\DeliveryBoyRevenueController;
use App\Http\Controllers\Api\v1\DeliveryBoy\GlobalDataController;
use App\Http\Controllers\Api\v1\DeliveryBoy\InitController;
use App\Http\Controllers\Api\v1\DeliveryBoy\NotificationController;
use App\Http\Controllers\Api\v1\DeliveryBoy\OrderController;
use App\Http\Controllers\Api\v1\DeliveryBoy\ProfileController;
use App\Http\Controllers\Api\v1\DeliveryBoy\ShopController;
use App\Http\Controllers\Api\v1\DeliveryBoy\ZoneController;
use App\Http\Controllers\Api\v1\NavigationController;
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

Route::group(['prefix' => 'v1/delivery_boy'], function () {
    //---------------- Auth --------------------//
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google_login', [AuthController::class, 'googleLogin']);
    Route::post('send_otp', [AuthController::class, 'sendOtp']);
    Route::post('login_via_otp', [AuthController::class, 'loginViaOtp']);

    Route::get('global_data/', [GlobalDataController::class, 'index']);

    Route::get('zones/', [ZoneController::class, 'index']);

    Route::group(['middleware' => ['guest_auth:delivery-boy-api']], function () {
        Route::get('init/', [InitController::class, 'index']);
    });

    Route::group(['middleware' => ['auth:delivery-boy-api']], function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('notifications/', [NotificationController::class, 'index']);


        Route::group([], function () {
            Route::get('dashboard/', [DashboardController::class, 'index']);
            Route::get('dashboard/revenues/', [DashboardController::class, 'revenues']);
        });

        Route::group([], function () {
            Route::resource('orders', OrderController::class)->only(['index', 'show']);
            Route::post('orders/{id}/accept/', [OrderController::class, 'accept']);
            Route::post('orders/{id}/reject/', [OrderController::class, 'reject']);
            Route::post('orders/{id}/pickup/', [OrderController::class, 'pickup']);
            Route::post('orders/{id}/deliver/', [OrderController::class, 'deliver']);
        });

        Route::group([], function () {
            Route::get('profile/', [ProfileController::class, 'show']);
            Route::patch('profile/', [ProfileController::class, 'update']);
            Route::patch('profile/update_location/', [ProfileController::class, 'updateLocation']);
            Route::patch('profile/update_status/', [ProfileController::class, 'updateStatus']);
            Route::post('profile/delete_account/', [ProfileController::class, 'deleteAccount']);
        });

        Route::group([], function () {
            Route::get('delivery_boy_revenues', [DeliveryBoyRevenueController::class, 'index']);
            Route::get('delivery_boy_payouts', [DeliveryBoyPayoutController::class, 'index']);
        });

        Route::group([], function () {
            Route::get('shops', [ShopController::class, 'show']);
        });

        //Chats
        Route::get('chats/', [ChatController::class, 'index']);
        Route::get('chats/with/', [ChatController::class, 'with']);
        Route::patch('chats/{chatId}/', [ChatController::class, 'update']);
        Route::get('chats/{chatId}/messages', [ChatMessageController::class, 'index']);
        Route::post('chats/{chatId}/messages', [ChatMessageController::class, 'create']);

        Route::patch('chats/{chatId}/messages/{id}/set_as_seen', [ChatMessageController::class, 'setAsSeen']);


        Route::post('navigation/get_route/', [NavigationController::class, 'get_route']);
    });
});


