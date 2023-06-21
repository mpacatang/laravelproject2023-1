<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

/**
 * @property mixed $discount
 * @property mixed $price
 * @property mixed $discount_type
 * @property float|mixed $calculated_price
 * @property float|mixed $total_discount
 * @property mixed $stock
 * @property Product $product
 * @property mixed $product_id
 */
class ProductOption extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];


    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        $rules = [
            'barcode' => [],
            'sku' => [],
            'stock' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['required_with:discount', 'in:percent,amount'],
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (request()->get('discount_type') == 'percent' && $value > 100) {
                        $fail('Percentage discount is not more than 100');
                    }
                },
            ],
            'option_value' => [],
        ];
        if ($id == null) {
            $rules['product_id'] = ['required'];
        }
        return $rules;
    }


    //======================= Getters ===========================================//


    public static function extractFromData(array $validated_data): array
    {
        $data = [
            'barcode' => Arr::get($validated_data, 'barcode'),
            'sku' => Arr::get($validated_data, 'sku'),
            'stock' => Arr::get($validated_data, 'stock'),
            'price' => Arr::get($validated_data, 'price'),
            'discount' => Arr::get($validated_data, 'discount'),
            'discount_type' => Arr::get($validated_data, 'discount_type'),
            'option_value' => Arr::get($validated_data, 'option_value'),
        ];
        return array_filter($data, fn($value) => !is_null($value) && $value !== '');
    }


    //===================== RelationShips  ====================================//


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,);
    }

    //===================== Boot Events  ====================================//


    protected static function _update(ProductOption $model)
    {
        if ($model->discount_type == 'percent') {
            $total_discount = ($model->price * $model->discount) / 100;
        } else {
            $total_discount = $model->discount;
        }
        $model->total_discount = $total_discount ?? 0;

        $model->calculated_price = $model->price - $model->total_discount;
    }


    protected static function boot()
    {
        parent::boot();
        self::updating(function (ProductOption $model) {
            self::_update($model);
        });
        self::creating(function (ProductOption $model) {
            self::_update($model);
        });
    }


}
