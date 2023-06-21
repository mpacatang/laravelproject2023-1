<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Strict\StrictProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ProductController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $name = $request->query('name');
        $productQuery = Product::withAll();

        return StrictProductResource::collection($productQuery->get());
    }

    public function similar(Request $request, $id): AnonymousResourceCollection
    {
        $module_id = $request->query('module_id');
        $productQuery = Product::withAll()->where('id', '!=', $id);
        if (isset($module_id)) {
            $productQuery = $productQuery->where('module_id', $module_id);
        }
        return ProductResource::collection($productQuery->inRandomOrder()->limit(5)->get());
    }

    public function show(Request $request, $id): StrictProductResource
    {
        $product = Product::withAll()->with(Product::getFavoriteRelation($this->getUserId()))->findOrFail($id);
        return new StrictProductResource($product);
    }

}

