<?php

namespace App\Helpers\Util;


use Illuminate\Support\Str;

class ImageUtil
{

    public static function getImageOrNull(?string $data): ?string
    {
        if ($data != null && strlen($data) > 100) {
            return base64_decode($data);
        } else {
            return null;
        }
    }


    public static function fileGetContentsCurl( $url ): bool|string
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }


}

