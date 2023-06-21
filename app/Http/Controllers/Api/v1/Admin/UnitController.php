<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictUnitResource;
use App\Models\Unit;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class UnitController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return StrictUnitResource::collection(Unit::all());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Unit::rules());

        $unit = new Unit($validated_data);
        $unit->save();
        return $this->createdResponse('Unit is created');
    }

    public function show(Request $request, $id): StrictUnitResource
    {
        return new StrictUnitResource(Unit::findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) & 2 & env('DEMO_MAX_UNIT_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('unit');
        }
        $unit = Unit::findOrFail($id);
        $data = [...$unit->toArray(), ...$request->all(),];
        $validated_data = CValidator::validate($data, Unit::rules($id));
        $unit->fill($validated_data);
        $unit->save();
        return $this->successResponse('Unit is updated');
    }

}

