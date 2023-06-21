<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddonResource;
use App\Models\Addon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class AddonController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $addons = Addon::with('shop');
        if ($module_id) {
            $addons = $addons->whereHas('shop', function ($q) use ($module_id) {
                $q->where('module_id', $module_id);
            });
        }
        return AddonResource::collection($addons->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Addon::rules());
        $addon = new Addon($validated_data);
        $addon->saveImageFromRequest($request);
        $addon->save();
        return $this->createdResponse('Addon is created');
    }


    public function show(Request $request, $id): AddonResource
    {
        return new AddonResource(Addon::with('shop')->findOrFail($id));
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_ADDON_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('addon');
        }
        $addon = Addon::findOrFail($id);
        $data = [...$addon->toArray(), ...$request->all(),];
        $validated_data = CValidator::validate($data, Addon::rules($id));
        $addon->fill($validated_data);
        $addon->saveImageFromRequest($request);
        $addon->save();
        return $this->successResponse('Addon is updated');
    }

    public function removeImage(Request $request, $id): Response|Application|ResponseFactory
    {
        $addon = Addon::findOrFail($id);
        $addon->removeImage();
        $addon->save();
        return $this->successResponse('Image is deleted');
    }


}

