<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Strict\StrictProductResource;
use App\Http\Resources\Strict\StrictProductReviewResource;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\SubCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $module_id = $request->get('module_id');
        $products = Product::withAll();
        if ($module_id) {
            $products = $products->where('module_id', $module_id);
        }
        return ProductResource::collection($products->latest()->get());
    }


    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, [...Product::rules(), ...ProductOption::rules(-1)]);

        $product_variant_id = null;
        if (isset($validated_data['product_variant_id'])) {
            $product_variant_id = $validated_data['product_variant_id'];
        }

        $shop = Shop::findOrFail($validated_data['shop_id']);

        $sub_category = SubCategory::findOrFail($validated_data['sub_category_id']);

        DB::transaction(function () use ($shop, $product_variant_id, $sub_category, $validated_data, $request) {
            if ($product_variant_id == null) {
                $product_variant = new ProductVariant();
                $product_variant->save();
                $product_variant_id = $product_variant->id;
            }

            $product_validated_data = Product::extractFromData($validated_data);
            $product_validated_data['product_variant_id'] = $product_variant_id;
            $product_validated_data['category_id'] = $sub_category->category_id;
            $product = new Product($product_validated_data);
            $product->module_id = $shop->module_id;
            $product->save();

            $addon_ids = $request->get('addons');
            if (isset($addon_ids)) {
                $product->updateAddons($addon_ids);
            }

            $option_data = ProductOption::extractFromData($validated_data);
            $option_data['product_id'] = $product->id;
            $product_option = new ProductOption($option_data);

            $product_option->save();

            $product->saveImages($request);
        });
        return $this->createdResponse('Product is created');
    }

    public function show(Request $request, $id): ProductResource
    {
        return new ProductResource(Product::withAll()->findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_PRODUCT_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('product');
        }
        $product = Product::findOrFail($id);
        $validated_data = $this->validate($request, Product::rules($id));
        $product->update($validated_data);
        $addons_ids = $request->get('addons');
        if (isset($addons_ids)) {
            $product->updateAddons($addons_ids);
        }
        $product->save();

        $product->saveImages($request);
        return $this->successResponse('product is updated');
    }

    public function removeAvailability(Request $request, $id): StrictProductResource
    {
        $product = Product::findOrFail($id);
        $product->available_from = null;
        $product->available_to = null;
        $product->save();
        $product->loadAll();
        return new StrictProductResource($product);
    }


    public function reviews(Request $request, $id): AnonymousResourceCollection
    {
        return StrictProductReviewResource::collection(
            ProductReview::with(['product', 'customer'])->where('product_id', $id)->get()
        );
    }

    public function validateImportData(Request $request): Response|Application|ResponseFactory
    {
        $data = Product::getValidatedArrayFromXLSXRequest($request);
        return is_array($data) ? $this->successResponse($data) : $this->errorResponse($data);
    }

    public function importData(Request $request): Response|\Exception|Application|ResponseFactory
    {
        $data = Product::getValidatedArrayFromXLSXRequest($request);
        return is_array($data) ? Product::uploadValidatedBulkData($data) : $this->errorResponse($data);
    }



}

