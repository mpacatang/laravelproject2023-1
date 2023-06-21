<?php

namespace App\Helpers\Request;

class CCurlRequest
{
    static function request($url, CCurlRequestType $type = CCurlRequestType::GET, $header=array(), $data = null, $userpwd=null): CCurl
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $userpwd);

        if ($type == CCurlRequestType::POST) {
            curl_setopt($ch, CURLOPT_POST, true);
        }
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $data = curl_exec($ch);

        $error = curl_error($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (!self::is_success($httpcode)) {
            $error = $data;
            $data = null;
        }
        curl_close($ch);
        return new CCurl($httpcode, $data, $error);


    }

    static function is_success(int $status): bool
    {
        return $status >= 200 && $status < 300;
    }


}




