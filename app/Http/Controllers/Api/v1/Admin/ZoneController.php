<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictZoneResource;
use App\Models\Zone;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class ZoneController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictZoneResource::collection(Zone::all());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Zone::rules());
        $polygon = $validated_data['polygon'];
        $zone = new Zone();
        $zone->name = $validated_data['name'];
        $zone->active = $validated_data['active'];

        $zone->coordinates = Zone::convertPolygonToCoords($polygon);
        $zone->save();
        return $this->createdResponse('Zone is created');
    }


    public function show(Request $request, $id): StrictZoneResource
    {
        return new StrictZoneResource(Zone::findOrFail($id));
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_ZONE_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('zone');
        }

        $validated_data = $this->validate($request, Zone::updateRules($id));
        $zone = Zone::findOrFail($id);

        if (isset($validated_data['polygon'])) {
            $polygon = $validated_data['polygon'];
            $zone->coordinates = Zone::convertPolygonToCoords($polygon);
        }

        $zone->name = $validated_data['name'];
        $zone->active = $validated_data['active'];
        DB::transaction(function () use ($zone) {
            if (!$zone->active) {
                $zone->makeAllRelatedInactive();
            }
            $zone->save();
        });
        return $this->successResponse('Zone is edited');
    }


}

