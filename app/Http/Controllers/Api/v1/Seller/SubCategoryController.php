<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class SubCategoryController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $this->user()->shop->module_id;
        return SubCategoryResource::collection(
            SubCategory::with('category')->whereHas('category', function ($q) use ($module_id) {
                $q->where('module_id', $module_id);
            })->get()
        );
    }


}

