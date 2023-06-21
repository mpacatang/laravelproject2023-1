<?php

namespace App\Helpers;

use App\Helpers\Request\CCurlRequest;
use App\Models\BusinessSetting;
use App\Models\NavigationCache;
use Exception;

class NavigationHelper
{

    public static function get_navigation($lat_1, $lng_1, $lat_2, $lng_2)
    {

        $navigation = NavigationCache::whereIn('lat_1', [$lat_1, $lat_2])->whereIn('lng_1', [$lng_1, $lng_2])->where('lat_2', [$lat_1, $lat_2])->where('lng_2', [$lng_1, $lng_2])->first();
        if ($navigation) {
            return $navigation->data;
        } else {

            $mapbox_key = BusinessSetting::_get(BusinessSetting::$mapbox_api_key);
            if (!$mapbox_key) {
                return CResponse::error("Mapbox key is not provided");
            }
            $url =
                "https://api.mapbox.com/directions/v5/mapbox/driving/" . $lng_1 . "," . $lat_1 . ";" . $lng_2 . "," . $lat_2 .
                "?alternatives=true&geometries=geojson&language=en&overview=simplified&steps=true&access_token=" . $mapbox_key;

            $response = CCurlRequest::request($url);
//            return $response->data;
            if ($response->is_success()) {
                $validated_data = [
                    'lat_1' => $lat_1,
                    'lat_2' => $lat_2,
                    'lng_1' => $lng_1,
                    'lng_2' => $lng_2,
                    'data' => $response->data,
                ];
                $navigation = new NavigationCache($validated_data);
                $navigation->save();
                return $navigation->data;
            } else {
                return CResponse::error(json_decode($response->error)->message);
            }
        }
    }

    public static function get_best_route($data)
    {
        $best_route = null;
        try {
            $routes = json_decode($data)->routes;
            foreach ($routes as $route) {
                if ($best_route == null) {
                    $best_route = $route;
                } elseif ($best_route->weight < $route->weight) {
                    $best_route = $route;
                }
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return $best_route;
    }

    public static function get_distance_in_km($route): float|int
    {
        return self::get_distance_in_meter($route) / 1000;
    }

    public static function get_distance_in_meter($route): float|int
    {
        return $route->distance;
    }

}
