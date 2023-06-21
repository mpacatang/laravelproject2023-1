<?php

namespace App\Models;

use App\Helpers\NavigationHelper;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


/**
 * @property mixed $tax
 * @property mixed $tax_type
 * @property mixed $need_prescription
 * @property mixed $longitude
 * @property mixed $latitude
 * @property mixed $minimum_delivery_charge
 * @property mixed $delivery_charge_multiplier
 * @property mixed $logo
 * @property string $cover_image
 * @property mixed $id
 * @property int $shop_plan_id
 * @property mixed $admin_commission
 * @property mixed $admin_commission_type
 * @property mixed $zone_id
 * @property mixed $module_id
 * @property mixed $self_delivery
 * @property mixed $active
 * @property mixed $open_for_delivery
 * @property mixed $take_away
 * @property mixed $packaging_charge
 * @property mixed $delivery_range
 * @property mixed $min_order_amount
 * @property false|mixed $approved
 * @property bool|mixed $archived
 * @property int|mixed $ratings_count
 * @property mixed $ratings_total
 * @property Module $module
 * @property Seller $owner
 * @method $this active()
 */
class Shop extends BaseModel
{
    use HasFactory;

    // --------------------------- Defaults ---------------------------------------//

    public static string $minutes = 'minutes';
    public static string $hours = 'hours';
    public static string $days = 'days';

    protected $guarded = [
        'sellers'
    ];

    protected $casts = [
        'active' => 'boolean',
        'open' => 'boolean',
        'open_for_delivery' => 'boolean',
        'self_delivery' => 'boolean',
        'take_away' => 'boolean',
    ];


    public static function withAll(): Builder|self
    {
        return static::with(['sellers', 'module', 'timings']);
    }

    public function loadAll(): Shop
    {
        return $this->load(['sellers', 'module', 'timings']);
    }

    // --------------------------- Rules ---------------------------------------//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'name' => ['required'],
            'module_id' => ['required', Rule::exists('modules', 'id')],
            'email' => ['required', 'email', 'unique:shops,email' . $extra_rule],
            'mobile_number' => ['required', 'unique:shops,mobile_number' . $extra_rule],
            'self_delivery' => ['boolean'],
            'zone_id' => ['required'],
            'active' => ['boolean'],
            'shop_plan_id' => ['required'],

            'description' => [],
            'address' => ['required'],
            'city' => ['required'],
            'take_away' => ['boolean'],
            'state' => ['required'],
            'country' => ['required'],
            'pincode' => ['required'],
            'license_number' => ['nullable', 'unique:shops,license_number' . $extra_rule],
            'packaging_charge' => ['numeric', 'min:0'],
            'tax_type' => ['required', 'in:percent,amount'],
            'tax' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ((request()->has('shop') && request()->get('shop')['tax_type'] == 'percent') || (request()->get(
                                'tax_type'
                            ) == 'percent')) {
                        if ($value > 100) {
                            $fail('Percentage tax is not more than 100');
                        }
                    }
                },
            ],

            'discount_type' => ['required_with:discount', 'in:percent,amount'],


            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'latitude' => ['required', 'unique:shops,latitude' . $extra_rule],
            'longitude' => ['required', 'unique:shops,longitude' . $extra_rule],
            'owner_id' => [],
            'open' => ['boolean'],
            'open_for_delivery' => ['boolean'],
            'delivery_range' => ['required_if:open_for_delivery,"true"', 'numeric', 'min:0',],
            'minimum_delivery_charge' => ['nullable', 'required_if:open_for_delivery,"true"', 'numeric', 'min:0'],
            'delivery_charge_multiplier' => ['nullable', 'required_if:open_for_delivery,"true"', 'numeric', 'min:0'],
            'min_delivery_time' => ['numeric', 'min:0'],
            'max_delivery_time' => ['numeric', 'min:0', 'gte:min_delivery_time'],
            'delivery_time_type' => []
        ];
    }


    //======================= Getters ===========================================//

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getDeliveryFee(CustomerAddress $address)
    {
        try {
            $lat_1 = $address->latitude;
            $lng_1 = $address->longitude;
            $lat_2 = $this->latitude;
            $lng_2 = $this->longitude;

            $data = NavigationHelper::get_navigation($lat_1, $lng_1, $lat_2, $lng_2);
            $route = NavigationHelper::get_best_route($data);

            if ($route != null) {
                $in_meter = NavigationHelper::get_distance_in_meter($route);
                if ($this->delivery_range < $in_meter) {
                    return -1;
                }

                $minimum_delivery_charge = $this->minimum_delivery_charge;
                $delivery_charge_multiplier = $this->delivery_charge_multiplier;
                if (!$this->self_delivery) {
                    $minimum_delivery_charge = BusinessSetting::_get(BusinessSetting::$minimum_delivery_charge);
                    $delivery_charge_multiplier = BusinessSetting::_get(BusinessSetting::$delivery_charge_multiplier);
                }


                return ($delivery_charge_multiplier * (NavigationHelper::get_distance_in_km(
                            $route
                        ))) + $minimum_delivery_charge;
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return null;
    }

    public function getTaxFromOrder($amount): float
    {
        if ($this->tax_type == 'percent') {
            return round(($amount * $this->tax) / 100, 2);
        } else {
            return round($this->tax, 2);
        }
    }

    public function getAdminCommissionFromOrder(float|int $amount): float
    {
        if ($this->admin_commission_type == 'percent') {
            return round(($amount * $this->admin_commission) / 100, 2);
        } else {
            return round($this->admin_commission, 2);
        }
    }


    //Permissions
    public function canAddProduct(): bool
    {
        $shop_plan = $this->activeShopPlan();
        return $shop_plan->products > $this->products()->count();
    }

    //Setter
    public function makeAllRelatedInactive()
    {
        $this->products()->update(['active' => false]);
        $this->addons()->update(['active' => false]);
    }



    //===================== Functionalities  ====================================//


    //Images
    public function saveLogo(Request $request, $key = 'logo'): bool
    {
        return parent::saveImageFromRequest(
            $request,
            imageBaseLocation: 'shop_logos/',
            modelKey: 'logo',
            requestImageKey: $key
        );
    }

    public function saveCoverImage(Request $request, $key = 'cover_image'): bool
    {
        return parent::saveImageFromRequest(
            $request,
            imageBaseLocation: 'shop_covers/',
            modelKey: 'cover_image',
            requestImageKey: $key
        );
    }


    public function removeLogo()
    {
        parent::removeImage(modelKey: 'logo');
    }

    public function removeCoverImage()
    {
        parent::removeImage(modelKey: 'cover_image');
    }


    public function attachOwner($seller_id = null)
    {
        if (isset($seller_id)) {
            $seller = Seller::where('is_owner', true)
                ->find($seller_id);

            if ($seller != null) {
                $this->sellers()->where('is_owner', true)->where('id', '!=', $seller_id)->update(['shop_id' => null]);
                $seller->shop_id = $this->id;
                $this->sellers()->save($seller);
            }
        }
    }

    public function upgradePlan($shop_plan_id = null)
    {
        $shop_plan_history = ShopPlanHistory::where('shop_id', $this->id)->where('ended_at', null)->first();
        $this->shop_plan_id = $shop_plan_id;

        if ($shop_plan_id != null) {
            $shop_plan = ShopPlan::findOrFail($shop_plan_id);

            $new_plan_history = new ShopPlanHistory(
                [
                    'started_at' => now(),
                    'shop_id' => $this->id,
                    'shop_plan_id' => $shop_plan_id,
                    'price' => $shop_plan->price
                ]
            );

            DB::transaction(function () use ($shop_plan, $new_plan_history, $shop_plan_history) {
                $this->admin_commission = $shop_plan->admin_commission;
                $this->admin_commission_type = $shop_plan->admin_commission_type;
                $this->save();
                if ($shop_plan_history != null) {
                    $shop_plan_history->ended_at = Carbon::now();
                    $shop_plan_history->duration = $shop_plan_history->ended_at->diffInSeconds(
                        $shop_plan_history->started_at
                    );
                    $shop_plan_history->save();
                }
                $new_plan_history->save();
            });
        } else {
            DB::transaction(function () use ($shop_plan_history) {
                $this->save();
                if ($shop_plan_history != null) {
                    $shop_plan_history->end_at = Carbon::now();
                    $shop_plan_history->save();
                }
            });
        }
    }


    //===================== RelationShips  ====================================//

    public static function getFavoriteRelation($customer_id = null): array
    {
        if ($customer_id == null) {
            return [];
        }
        return [
            'customerFavoriteShops' => function ($hasMany) use ($customer_id) {
                $hasMany->where('customer_id', $customer_id);
            }
        ];
    }

    public function customerFavoriteShops(): HasMany
    {
        return $this->hasMany(CustomerFavoriteShop::class, 'shop_id', 'id');
    }


    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }


    public function owner(): HasOne
    {
        return $this->hasOne(Seller::class)->where('is_owner', true);
    }


    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function shopPlan(): BelongsTo
    {
        return $this->belongsTo(ShopPlan::class);
    }

    public function activeShopPlan(): ShopPlan
    {
        return (ShopPlanHistory::with('shopPlan')->where('shop_id', $this->id)
            ->where('ended_at', '=', null)->firstOrFail())->shopPlan;
    }


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(Addon::class);
    }

    public function timings(): HasMany
    {
        return $this->hasMany(ShopTime::class);
    }

}
