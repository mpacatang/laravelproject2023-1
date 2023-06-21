<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class CategoryController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->query('module_id');
        $categories = Category::with('subCategories');
        if ($module_id != null) {
            $categories->where('module_id', $module_id);
        }
        return CategoryResource::collection($categories->get());
    }

}

