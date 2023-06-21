<?php

namespace App\Helpers\Auth;

use App\Models\TokenLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;


trait HasTokenLog
{
    public function createToken($fcm_token = null): TokenLog
    {
        return TokenLog::login($this->id, static::class, $fcm_token);
    }


    public function deleteToken($fcm_token = null): void
    {
        TokenLog::logout($this->id, static::class, $fcm_token);
    }


    public function tokenLogs(): MorphMany
    {
        return $this->morphMany(TokenLog::class, 'tokenable');
    }

    public function activeTokenLogs(): MorphMany
    {
        return $this->morphMany(TokenLog::class, 'tokenable')->where('logout_at', null);
    }

}
