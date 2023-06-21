<?php


use App\Http\Controllers\Api\v1\PaymentCaptureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


require_once "admin_api.php";
require_once "seller_api.php";
require_once "customer_api.php";
require_once "delivery_boy_api.php";
require_once "installation_api.php";
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//======= TODO ====  Uncomment At Production ========== //
Route::post('/payment_capture/razorpay', [PaymentCaptureController::class, 'razorpay']);
Route::post('/payment_capture/stripe', [PaymentCaptureController::class, 'stripe']);
Route::post('/payment_capture/paypal', [PaymentCaptureController::class, 'paypal']);
Route::post('/payment_capture/flutterwave', [PaymentCaptureController::class, 'flutterwave']);
Route::post('/payment_capture/paystack', [PaymentCaptureController::class, 'paystack']);
Route::post('/payment_capture/cashfree', [PaymentCaptureController::class, 'cashfree']);



Route::post('/', [PaymentCaptureController::class, 'test']);



