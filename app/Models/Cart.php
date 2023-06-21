<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property ProductVariant $productVariant
 * @property mixed $quantity
 * @property ProductOption $productOption
 * @property CartAddon[] $addons
 */
class Cart extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    public static function withAll(): Builder|self
    {
        return static::with(
            ['productOption', 'product', 'product.productImages', 'shop', 'product.addons', 'addons', 'addons.addon']
        );
    }

    public function loadAll(): Cart
    {
        return $this->load(
            ['productOption', 'product', 'product.productImages', 'shop', 'product.addons', 'addons', 'addons.addon']
        );
    }


    //===================== Rules  ====================================//


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'quantity' => ['numeric', 'min:0'],
            'customer_id' => ['required'],
            'product_option_id' => ['required'],
        ];
    }

    public static function updateQuantityRules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'quantity' => ['required', 'numeric', 'min:0'],
        ];
    }


    //===================== Functionalities  ====================================//

    public function getCartTotal(): float|int
    {
        return $this->getAddonTotal() + $this->getProductTotal();
    }

    public function getAddonTotal(): float|int
    {
        $total = 0;
        if ($this->addons != null) {
            foreach ($this->addons as $addon) {
                $total += $addon->getTotal();
            }
        }
        return $total;
    }

    public function getProductTotal(): float|int
    {
        return $this->quantity * $this->productOption->calculated_price;
    }

    //===================== RelationShips  ====================================//


    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(CartAddon::class);
    }

}
