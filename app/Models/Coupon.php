<?php

namespace App\Models;

use App\Jobs\ProcessCouponExpire;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static $this active()
 * @property mixed $discount
 * @property mixed $max_discount
 * @property mixed $discount_type
 * @property mixed $min_order
 * @property mixed $orders
 * @property mixed $first_order
 * @property mixed $limit
 * @property Carbon|mixed $expired_at
 * @property Carbon|mixed $started_at
 * @property mixed $delivery_free
 * @property false|mixed $active
 */
class Coupon extends BaseModel
{
    use HasFactory;

    // --------------------------- Defaults ---------------------------------------//

    protected $guarded = [];

    protected $casts = [
        'first_order' => 'boolean',
        'delivery_free' => 'boolean',
        'active' => 'boolean',
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // --------------------------- Rules ---------------------------------------//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'code' => ['required', 'unique:coupons,code' . $extra_rule],
            'description' => [],
            'active' => ['boolean'],
            'delivery_free' => ['required', 'boolean'],
            'discount' => [
                'nullable',
                'required_if:delivery_free,false',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if (request()->get('discount_type') == 'percent' && $value > 100) {
                        $fail('Percentage discount is not more than 100');
                    }
                },
            ],
            'discount_type' => ['in:percent,amount', 'required_if:delivery_free,false'],
            'min_order' => ['nullable', 'required_if:delivery_free,false', 'numeric', 'min:0'],
            'max_discount' => ['required_if:delivery_free,false'],
            'first_order' => ['boolean'],

            'limit' => ['required_if:first_order,false', 'numeric', 'min:1'],
            'started_at' => ['required', 'date'],
            'expired_at' => ['required', 'date', 'after:started_at'],
            'shop_id' => [],
            'module_id' => [],
            'zone_id' => [],
        ];
    }

    public static function ruleMessages($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'discount.required_if' => 'The discount field is required',
            'discount_type.required_if' => 'The discount type field is required',
            'min_order.required_if' => 'The min_order field is required',
            'max_discount.required_if' => 'The max_discount field is required',
            'limit.required_if' => 'The limit field is required, if it\'s not a first order',
        ];
    }



    // ------------------------- Functions ---------------------------------------------------//

    //Scoped Functions laravel, admin, flutter, android, ios, delivery, food, shopping, grocery, pharmacy, multi vendor, admin panel, seller app, delivery app, customer web

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function verify(): bool
    {
        if ($this->first_order && sizeof($this->orders) != 0) {
            return false;
        }
        if ($this->limit != null) {
            $count = 0;
            $this->with(['orders']);
            foreach ($this->orders as $order) {
                if ($order->coupon_id == $this->id) {
                    $count++;
                }
            }
            if ($count >= $this->limit) {
                return false;
            }
        }
        return true;
    }


    public function getDiscountFromOrder($order, $delivery_charge = 0): float|int
    {
        if ($this->delivery_free) {
            return $delivery_charge;
        }
        if ($order < $this->min_order) {
            return 0;
        }
        if ($this->discount_type == 'percent') {
            $value = ($order * $this->discount) / 100;
            if ($value > $this->max_discount) {
                return round($this->max_discount, 2);
            }
            return round($value, 2);
        } else {
            return round($this->discount, 2);
        }
    }


    // ------------------------- Relationships ---------------------------------------------------//


    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ------------------------- Life Cycle ---------------------------------------------------//

    protected static function boot()
    {
        parent::boot();

        self::saved(function ($model) {
            ProcessCouponExpire::dispatch($model->id)->delay($model->expired_at);
        });
    }

}
