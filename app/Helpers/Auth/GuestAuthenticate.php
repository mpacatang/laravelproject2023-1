<?php

namespace App\Helpers\Auth;

use App\Helpers\CResponse;
use App\Models\Admin;
use App\Models\DeliveryBoy;
use App\Models\Seller;
use App\Models\TokenLog;
use App\Models\Customer;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/**
 * @property Auth $auth
 */
class GuestAuthenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @return string|null
     * @throws AuthenticationException
     */

    private array $models = array(
        'admin-api' => Admin::class,
        'seller-api' => Seller::class,
        'customer-api' => Customer::class,
        'delivery-boy-api' => DeliveryBoy::class
    );

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $authorization = $request->headers->get('authorization');

        if (isset($authorization)) {
            $token = explode("Bearer ", $authorization);
            if (sizeof($token) > 1 and sizeof($guards) > 0) {
                $token = $token[1];
                $tokenGuard = strval($guards[0]);
                $morph= $this->models[$tokenGuard];

                $userToken = TokenLog::where('access_token', $token)->where('tokenable_type', $morph)->first();
                if ($userToken) {
                    $user = $morph::find($userToken->tokenable_id);

//                    if (isset($user->active)) {
//                        if (!$user->active) {
//                            return $this->deactivated($request, $guards);
//                        }
//                    }
                    if ($user) {
                        $this->bindAuthenticatedUser($user);
                        $request->merge(['userId' => $user->id, 'user_id' => $user->id]);
                        if ($morph === Customer::class) {
                            $request->merge(['customer_id' => $user->id]);
                        } elseif ($morph == Seller::class) {
                            $request->merge(['seller_id' => $user->id]);
                        }
                        $request->setUserResolver(function () use ($user) {
                            return $user;
                        });
                        return $next($request);
                    }
                }
            }
        }
        $this->bindAuthenticatedUser(null);
        $request->setUserResolver(function () {
            return null;
        });
        return $next($request);


    }

    protected function deactivated($request, array $guards): Response|Application|ResponseFactory
    {
        return Response("This account is disabled by admin", CResponse::$DISABLED);
    }

    private function bindAuthenticatedUser($user): void
    {
        Route::bind('user', function ($id) use ($user) {
            return $user;
        });
    }

    /**
     * @throws AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }

    protected function redirectTo($request): ?string
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
        return null;
    }
}
