<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductOptionResource;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class ProductOptionController extends Controller
{

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Application|ResponseFactory|Response|ProductOptionResource
    {
        $validated_data = $this->validate($request, ProductOption::rules());
        if (ProductOption::where('product_id', $validated_data['product_id'])->where(
            'option_value',
            $validated_data['option_value']
        )->exists()) {
            return $this->validationErrorResponse(['option_value' => "This option is already used. Use other"]);
        }
        $product_option = new ProductOption($validated_data);
        $product_option->save();
        return new ProductOptionResource($product_option);
    }

    public function show(Request $request, $id): ProductVariantResource
    {
        return new ProductVariantResource(
            ProductVariant::with(['shop', 'productImages', 'product', 'productOptions'])->findOrFail($id)
        );
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory|ProductOptionResource
    {
        $product_option = ProductOption::findOrFail($id);
        $validated_data = CValidator::validate([...$product_option->toArray(), ...$request->all()],
            ProductOption::rules($id));
        if (ProductOption::where('product_id', $product_option->product_id)
            ->where('option_value', $validated_data['option_value'])->where('id', '!=', $id)->exists()) {
            return $this->validationErrorResponse(['option_value' => "This option is already used. Use other"]);
        }
        $product_option->update($validated_data);
        $product_option->save();
        return new ProductOptionResource($product_option);
    }


}

