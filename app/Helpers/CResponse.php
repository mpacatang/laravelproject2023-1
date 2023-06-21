<?php

namespace App\Helpers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class CResponse
{
    static int $OK = 200;
    static int $CREATED = 201;
    static int $ACCEPTED = 202;
    static int $NO_CONTENT = 204;


    static int $SELLER_HAS_NOT_SHOP = 309;
    static int $DISABLED = 310;
    static int $SHOP_NOT_ACTIVE = 311;
    static int $SELF_DELIVERY_NOT_ACTIVE = 312;
    static int $SHOP_PLAN_NEED_UPGRADE = 313;

    static int $EMAIL_NOT_VERIFIED = 320;
    static int $MOBILE_NUMBER_NOT_VERIFIED = 321;

    static int $INSTALLATION_ERROR = 350;
    static int $INSTALLATION_FALLBACK = 351;

    static int $BAD_REQUEST = 400;
    static int $UNAUTHORIZED = 401;
    static int $FORBIDDEN = 403;
    static int $NOT_FOUND = 404;
    static int $ENTITY = 422;

    /**
     * @deprecated
     */
    public static function validation_error($errors): Response|Application|ResponseFactory
    {
        return response(['errors' => $errors], CResponse::$ENTITY);
    }

    /**
     * @deprecated
     */
    public static function error($error = 'Something went wrong!!!'): Response|Application|ResponseFactory
    {
        return response(['error' => $error], CResponse::$BAD_REQUEST);
    }

    /**
     * @deprecated
     */
    public static function no_content(): Response|Application|ResponseFactory
    {
        return response('', CResponse::$NO_CONTENT);
    }

    /**
     * @deprecated
     */
    public static function success($message = ''): Response|Application|ResponseFactory
    {
        return response($message, CResponse::$OK);
    }

    /**
     * @deprecated
     */
    public static function created($message = ''): Response|Application|ResponseFactory
    {
        return response($message, CResponse::$CREATED);
    }

    /**
     * @deprecated
     */
    public static function accepted($message = ''): Response|Application|ResponseFactory
    {
        return response($message, CResponse::$ACCEPTED);
    }

    /**
     * @deprecated
     */
    public static function shopNotActive($message = ''): Response|Application|ResponseFactory
    {
        return response($message, CResponse::$SHOP_NOT_ACTIVE);
    }


    public static function selfDeliveryNotActive($message = ''): Response|Application|ResponseFactory
    {
        return response($message, CResponse::$SELF_DELIVERY_NOT_ACTIVE);
    }


    public static function shopPlanNeedUpgrade($message = 'You need to upgrade plan'): Response|Application|ResponseFactory {
        return response($message, CResponse::$SHOP_PLAN_NEED_UPGRADE);
    }


    public static function demoError($message = 'You can\'t change in demo'): Response|Application|ResponseFactory
    {
        return self::error($message);
    }

    public static function demoCreateNewError($item = 'item'): Response|Application|ResponseFactory
    {
        return self::error('- You can\'t change in demo ' . $item . '. Please create new one and try to change');
    }

    /**
     * @deprecated
     */
    public static function installationFallback($item = 'step_1'): Response|Application|ResponseFactory
    {
        return response(['fallback' => $item], CResponse::$INSTALLATION_FALLBACK);
    }


}
