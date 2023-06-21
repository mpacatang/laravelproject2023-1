<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $till_date
 * @property float|int|mixed $take_from_admin
 * @property HigherOrderBuilderProxy|int|mixed $pay_to_admin
 * @property mixed $delivery_boy_id
 * @property float|int|mixed $pay_to_shop
 * @property HigherOrderBuilderProxy|int|mixed $take_from_shop
 */
class DeliveryBoyPayout extends BaseModel
{

    //===================== Defaults  ====================================//

    protected $guarded = [];

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'delivery_boy_id' => ['required'],
            'amount' => ['required'],
            'till_date' => ['required', 'date',],
        ];
    }

    //===================== RelationShips  ====================================//

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class);
    }

}
