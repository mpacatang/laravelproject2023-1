<?php

namespace App\Http\Middleware;

use App\Helpers\CResponse;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;


class CheckSellerRolePermission
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return Application|ResponseFactory|CResponse|mixed
     */
    public function handle(Request $request, Closure $next, $permission): mixed
    {
        $user = $request->user();
        if ($user->is_owner) {
            return $next($request);
        }
        $role = $user->role;
        if ($role != null && $role->hasPermission($permission)) {
            return $next($request);
        }
        return Response("You haven't access to this resource", CResponse::$FORBIDDEN);
    }
}
