<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property mixed|string $access_token
 * @property mixed $tokenable_type
 * @property mixed $tokenable_id
 * @property mixed|null $fcm_token
 * @property Carbon|mixed $logout_at
 */
class TokenLog extends BaseModel
{


    public static function login($tokenable_id, $tokenable_type, $fcm_token = null): TokenLog
    {
        $token_log = new TokenLog();
        $token_log->tokenable_id = $tokenable_id;
        $token_log->tokenable_type = $tokenable_type;
        $token_log->access_token = Str::random(120);
        $token_log->fcm_token = $fcm_token;
        $token_log->save();
        return $token_log;
    }

    public static function logout($tokenable_id, $tokenable_type, $fcm_token = null)
    {
        if ($fcm_token != null) {
            $user_log = TokenLog::where('tokenable_id', $tokenable_id)->where('tokenable_type', $tokenable_type)->where(
                'fcm_token',
                $fcm_token
            )->first();
            if ($user_log) {
                $user_log->logout_at = now();
                $user_log->save();
            }
        }
    }

    //===================== Functionalities  ====================================//

    public static function getAllToken($tokenable_type): array
    {
        return TokenLog::where('tokenable_type', $tokenable_type)
            ->where('logout_at', null)->pluck('fcm_token')->toArray();
    }

    public static function getUserFCMTokens($tokenable_type, $tokenable_id): array
    {
        return TokenLog::where('tokenable_type', $tokenable_type)
            ->where('tokenable_id', $tokenable_id)->where('logout_at', null)->pluck('fcm_token')->toArray();
    }

    public static function getUserFromToken($tokenable_type, $token)
    {
        return TokenLog::with('tokenable')->where('tokenable_type', $tokenable_type)
            ->where('access_token', $token)->first()?->tokenable;
    }

    //===================== RelationShips  ====================================//

    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }


}
