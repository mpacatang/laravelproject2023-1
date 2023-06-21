<?php

namespace App\Http\Middleware;

use App\Helpers\CResponse;

use App\Models\Shop;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;


class EnsureSellerHasShop
{

    /**
     * @param Request $request
     * @param Closure $next
     * @return Application|ResponseFactory|CResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $shop_id = $request->user()->shop_id;
        $shop = Shop::findOrFail($shop_id);
        if (isset($shop_id)) {
            if($shop->active){
                $request->merge(['shop_id' => $shop_id]);
                return $next($request);
            }elseif ($request->expectsJson()) {
                    return Response("Your shop is closed by admin", CResponse::$SHOP_NOT_ACTIVE);

            }
        } elseif ($request->expectsJson()) {
                return Response("This seller has not any shop", CResponse::$SELLER_HAS_NOT_SHOP);
        }

        return Response("This seller has not any shop", CResponse::$SELLER_HAS_NOT_SHOP);
    }
}
