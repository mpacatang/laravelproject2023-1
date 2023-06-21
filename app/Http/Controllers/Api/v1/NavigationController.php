<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\NavigationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class NavigationController extends Controller
{

    /**
     * @throws ValidationException
     */
    public function get_route(Request $request)
    {
        $validated_data = $this->validate($request, [
            'lat_1' => ['required'],
            'lat_2' => ['required'],
            'lng_1' => ['required'],
            'lng_2' => ['required'],
        ]);

        $lat_1 = $validated_data['lat_1'];
        $lng_1 = $validated_data['lng_1'];
        $lat_2 = $validated_data['lat_2'];
        $lng_2 = $validated_data['lng_2'];

        return NavigationHelper::get_navigation($lat_1, $lng_1, $lat_2, $lng_2);
    }

}

