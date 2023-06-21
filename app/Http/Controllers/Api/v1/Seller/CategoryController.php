<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class CategoryController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            Category::with('subCategories')
            ->where('module_id', $this->user()->shop->module_id)->get());
    }

}

