<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $till_date
 * @property float|int|mixed $take_from_shop
 * @property HigherOrderBuilderProxy|int|mixed $pay_to_shop
 * @property mixed $shop_id
 */
class ShopPayout extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected $dates = [
        'till_date'
    ];

    //===================== Rules  ====================================//

    public static function rules(): array
    {
        return [
            'shop_id' => ['required'],
            'amount' => ['required'],
            'till_date' => ['required', 'date', ],
        ];
    }

    //===================== RelationShips  ====================================//

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }


}
