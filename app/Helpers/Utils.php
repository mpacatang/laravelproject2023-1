<?php

namespace App\Helpers;


class Utils
{

    public static function getEnv(string $key){
        return env($key);
    }


    static function distanceBetweenTwoPoint($shop, $customerAddress): float|int|string
    {
        return self::distanceBetweenTwoLatLng($shop->latitude, $shop->longitude, $customerAddress->latitude, $customerAddress->longitude);
    }

    static function distanceBetweenTwoLatLng($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo): float|int|string
    {

        $long1 = deg2rad($longitudeFrom);
        $long2 = deg2rad($longitudeTo);
        $lat1 = deg2rad($latitudeFrom);
        $lat2 = deg2rad($latitudeTo);

        //Haversine Formula
        $dlong = $long2 - $long1;
        $dlati = $lat2 - $lat1;

        $val = pow(sin($dlati / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($dlong / 2), 2);

        $res = 2 * asin(sqrt($val));

        $radius = 6371000;

        return $res * $radius;

    }


}