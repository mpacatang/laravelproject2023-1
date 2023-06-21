<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $id
 * @property HigherOrderBuilderProxy|mixed $quantity
 * @property mixed $order_id
 * @property HigherOrderBuilderProxy|mixed $product_id
 * @property HigherOrderBuilderProxy|mixed $effective_price
 * @property HigherOrderBuilderProxy|mixed $discount_type
 * @property HigherOrderBuilderProxy|mixed $discount
 * @property HigherOrderBuilderProxy|mixed $price
 * @property mixed $calculated_price
 * @property HigherOrderBuilderProxy|mixed $product_option_id
 * @property HigherOrderBuilderProxy|mixed $product_variant_id
 * @property Product $product
 * @property ProductOption $productOption
 */
class OrderItem extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];


    //===================== RelationShips  ====================================//


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(OrderItemAddon::class);
    }

}
