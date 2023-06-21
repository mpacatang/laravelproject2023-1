<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\SpatialBuilder;

/**
 * @property Polygon|mixed $coordinates
 * @property mixed $name
 * @property mixed $active
 * @property Shop[] $shops
 */
class Zone extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $casts = [
        'coordinates' => Polygon::class,
        'active' => 'boolean'
    ];

    public function newEloquentBuilder($query): SpatialBuilder
    {
        return new SpatialBuilder($query);
    }



    /// =================== Rules ===================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'name' => ['required', 'unique:zones,name' . $extra_rule],
            'active' => ['boolean'],
            'polygon' => ['required'],
        ];
    }

    public static function updateRules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'name' => ['required', 'unique:zones,name' . $extra_rule],
            'active' => ['boolean'],
            'polygon' => [],
        ];
    }


    //===================== Functionalities  ====================================//

    public function makeAllRelatedInactive()
    {
        foreach ($this->shops as $shop) {
            $shop->makeAllRelatedInactive();
            $shop->active = false;
            $shop->save();
        }
    }


    public static function convertPolygonToCoords($polygon): Polygon
    {
        $coords = explode(';', $polygon);
        $points = [];
        $first = null;
        foreach ($coords as $coord) {
            $point = explode(',', $coord);
            if (!$first) {
                $first = new Point(floatval($point[0]), floatval($point[1]));
            }
            $points[] = new Point(floatval($point[0]), floatval($point[1]));
        }
        $points[] = $first;
        return new Polygon([new LineString($points)]);
    }


    //===================== RelationShips  ====================================//

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

}
