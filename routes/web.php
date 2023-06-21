<?php

use App\Http\Controllers\Api\v1\Customer\ProfileController;
use App\Http\Controllers\Api\v1\PaymentCaptureController;
use App\Http\Controllers\TestController;
use App\Http\Resources\Strict\StrictBusinessSettingResource;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/test',[TestController::class,'test']);
//Route::get('/', [TestController::class, 'test']);
Route::post('/', [PaymentCaptureController::class, 'test']);


// Route::get('/test', [TestController::class, 'test']);

Route::get('customer/profile/verify_email/', [ProfileController::class, 'verifyEmail'])->name(
    'customer.profile.verify_email'
);
//Route::get('reset_password/{token}', [ResetPasswordController::class, 'form'])->name('reset_password.form');
//Route::post('reset_password', [ResetPasswordController::class, 'reset'])->name('reset_password.reset');


Route::get('payments/success/', function () {
    return view('payment_success');
})->name('payments.success');

Route::get('/{any?}', function () {
    return view('welcome')->with([
            'business_settings' => (new StrictBusinessSettingResource(BusinessSetting::getInstance()))->toJson(),
            'google_analytics_enable' => BusinessSetting::_get(BusinessSetting::$google_analytics_enable, false),
            'google_analytics_id' => BusinessSetting::_get(BusinessSetting::$google_analytics_id,)
        ]
    );
})->where('any', '^(?!api\/)[\/\w\.-]*');

