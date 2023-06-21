<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class SubCategoryController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $sub_categories = SubCategory::with('category');
        if ($module_id) {
            $sub_categories = $sub_categories->whereHas('category', function ($q) use ($module_id,) {
                $q->where('module_id', $module_id);
            });
        }

        return SubCategoryResource::collection($sub_categories->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, SubCategory::rules());
        $sub_category = new SubCategory($validated_data);
        $sub_category->save();
        return $this->createdResponse('Sub category is created');
    }

    public function show(Request $request, $id): SubCategoryResource
    {
        return new SubCategoryResource(SubCategory::with('category')->findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_SUB_CATEGORY_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('sub category');
        }

        $sub_category = SubCategory::find($id);
        $validated_data = $this->validate($request, SubCategory::rules($id));
        $sub_category->update($validated_data);
        $sub_category->save();
        return $this->successResponse('Sub category is edited');
    }


}

