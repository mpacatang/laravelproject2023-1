<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $quantity
 * @property mixed $price
 * @property mixed $addon_id
 * @property mixed $order_item_id
 */
class OrderItemAddon extends BaseModel
{
    //===================== RelationShips  ====================================//

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class);
    }
}
