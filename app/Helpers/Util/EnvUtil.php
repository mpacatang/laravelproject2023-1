<?php

namespace App\Helpers\Util;

use Exception;
use Illuminate\Support\Facades\Artisan;

class EnvUtil
{
    public static function changeEnvVariable($key, $value): bool
    {
        try {
            Artisan::call('config:cache');
            Artisan::call('config:clear');

            if (is_bool(env($key,))) {
                $old = env($key) ? 'true' : 'false';
            } elseif (env($key) === null) {
                $old = '';
            } else {
                $old = env($key);
            }

            if (file_exists(app()->environmentFilePath())) {
                file_put_contents(app()->environmentFilePath(), str_replace(
                    "$key=" . $old, "$key=" . $value, file_get_contents(app()->environmentFilePath())
                ));
            }
        } catch (Exception $e) {
            error_log($e);
            return false;
        }

        return true;

    }


}