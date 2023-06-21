<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class ModuleController extends Controller
{

    public function index(): AnonymousResourceCollection
    {
        return ModuleResource::collection(Module::with(['products', 'shops'])->get());
    }

    public function show($id): ModuleResource
    {
        return new ModuleResource(Module::findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        $module = Module::findOrFail($id);
        $validated_data = $this->validate($request, Module::rules($id));
        $module->title = $validated_data['title'];
        $module->active = $validated_data['active'];
        DB::transaction(function () use ($module) {
            if (!$module->active) {
                $module->makeAllRelatedInactive();
            }
            $module->save();
        });

        return $this->successResponse();
    }
}

