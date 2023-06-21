<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|mixed $end_at
 * @property mixed $started_at
 * @property float|int|mixed $duration
 * @property Carbon|mixed $ended_at
 */
class ShopPlanHistory extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];


    //===================== RelationShips  ====================================//

    public function shopPlan(): BelongsTo
    {
        return $this->belongsTo(ShopPlan::class);
    }


    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }


}
