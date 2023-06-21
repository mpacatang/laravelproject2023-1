<?php

namespace App\Providers;

use App\Http\Controllers\Api\v1\Admin\AuthController;
use App\Http\Controllers\Api\v1\InstallationController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        if ($this->needToInstallation()) {
            Route::get('/installations/{any?}', function () {
                return view('welcome');
            });
        } else {
            Route::get('/installations/{any?}', function () {
                return view('installation_completed');
            });
        }

        if ($this->needToInstallation()) {
            Route::prefix('api')->middleware('api')->group(base_path('routes/installation_api.php'));
            Route::prefix('api')->middleware('api')->group(function () {
                Route::post('/v1/admin/login', [AuthController::class, 'login']);

            });


            Route::get('/installations/{any?}', function () {
                return view('welcome');
            });

            $this->routes(function () {
                Route::get('/{any?}', function () {
                    return redirect()->to('/installations/step_1');
                });
            });
        } else {


            $this->routes(function () {
                Route::prefix('api')->middleware('api')->group(base_path('routes/api.php'));

                Route::middleware('web')->group(base_path('routes/web.php'));
            });
        }
    }

    protected function needToInstallation(): bool
    {
        $need_version = InstallationController::$NEED_VERSION;
        $c_version = env('VERSION');
        return version_compare($c_version, $need_version) < 0;
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
