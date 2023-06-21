<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 */
class ChatParticipant extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    public static function withAll(): Builder|self
    {
        return static::with(
            []
        );
    }

    public function loadAll(): ChatParticipant
    {
        return $this->load(
            []
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


    //===================== Functionalities  ====================================//

    public function getUserType(): string
    {
        return array(
            Admin::class => 'admin',
            Seller::class => 'seller',
            Customer::class => 'customer',
            DeliveryBoy::class => 'delivery_boy',
        )[$this->user_type];
    }


    //===================== RelationShips  ====================================//


    public function user(): MorphTo
    {
        return $this->morphTo();
    }

}
