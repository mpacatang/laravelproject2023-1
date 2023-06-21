<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Addon $addon
 * @property mixed $quantity
 */
class CartAddon extends BaseModel
{

    //===================== Defaults  ====================================//

    protected $guarded = [];


    //===================== Rules  ====================================//


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'cart_id' => ['required', 'exists:carts,id'],
            'addon_id' => ['required', 'exists:addons,id'],

        ];
    }

    public static function updateQuantityRules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    //===================== Functionalities  ====================================//


    public function getTotal(): float
    {
        if ($this->addon != null) {
            return $this->addon->price * $this->quantity;
        }
        return 0;
    }

    //===================== RelationShips  ====================================//

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }


}
