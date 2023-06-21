<?php

namespace App\Helpers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Kreait\Firebase\Auth\UserRecord;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;


class FirebaseHelper
{

    public static function getUserFromUID($token): Application|ResponseFactory|Response|UserRecord|null
    {
        try {
            $factory = (new Factory())->withServiceAccount('../firebase_credentials.json');
            return $factory->createAuth()->getUser($token);
        } catch (AuthException|FirebaseException $e) {
            return null;
        }
    }


}

