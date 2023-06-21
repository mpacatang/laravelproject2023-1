<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\ResourceUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $description
 * @property mixed $name
 * @property mixed $id
 * @property mixed $tax
 * @property mixed $delivery_charge_multiplier
 * @property mixed $minimum_delivery_charge
 * @property mixed $delivery_range
 * @property mixed $open_for_delivery
 * @property mixed $longitude
 * @property mixed $latitude
 * @property mixed $pincode
 * @property mixed $country
 * @property mixed $state
 * @property mixed $city
 * @property mixed $address
 * @property mixed $open
 * @property mixed $mobile_number
 * @property mixed $email
 * @property mixed $need_prescription
 * @property mixed $tax_type
 * @property mixed $ratings_count
 * @property mixed $ratings_total
 * @property mixed $delivery_time_type
 * @property mixed $max_delivery_time
 * @property mixed $min_delivery_time
 * @property mixed $rating
 * @property mixed $module_id
 * @property mixed $take_away
 * @property mixed $cover_image
 * @property mixed $logo
 * @property mixed $shop_plan_id
 * @property mixed $admin_commission_type
 * @property mixed $admin_commission
 * @property mixed $min_order_amount
 * @property mixed $zone_id
 * @property mixed $active
 * @property mixed $packaging_charge
 * @property mixed $self_delivery
 * @property mixed $approved
 */
class StrictShopResource extends JsonResource
{


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */


    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'active' => $this->active,
            'approved' => $this->approved,
            'name' => $this->name,
            'email' => $this->email,
            'mobile_number' => $this->mobile_number,
            'description' => $this->description,
            'open' => $this->open,
            'module_id' => $this->module_id,
            'zone_id' => $this->zone_id,
            'shop_plan_id' => $this->shop_plan_id,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'pincode' => $this->pincode,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'open_for_delivery' => $this->open_for_delivery,
            'take_away' => $this->take_away,
            'self_delivery' => $this->self_delivery,
            'delivery_range' => $this->delivery_range,
            'min_delivery_time' => $this->min_delivery_time,
            'max_delivery_time' => $this->max_delivery_time,
            'delivery_time_type' => $this->delivery_time_type,
            'minimum_delivery_charge' => $this->minimum_delivery_charge,
            'delivery_charge_multiplier' => $this->delivery_charge_multiplier,
            'packaging_charge' => $this->packaging_charge,
            'tax' => $this->tax,
            'tax_type' => $this->tax_type,
            'admin_commission' => $this->admin_commission,
            'admin_commission_type' => $this->admin_commission_type,
            'min_order_amount' => $this->min_order_amount,
            'rating' => $this->rating,
            'ratings_total' => $this->ratings_total,
            'ratings_count' => $this->ratings_count,
            'need_prescription' => $this->need_prescription,
            'logo' => ResourceUtil::getImagePath($this->logo),
            'cover_image' => ResourceUtil::getImagePath($this->cover_image),
            'shop_plan' => StrictShopPlanResource::collection($this->whenLoaded('shopPlan')),
            'timings' => StrictShopTimeResource::collection($this->whenLoaded('timings')),
            'products' => StrictProductResource::collection($this->whenLoaded('products')),
            'module' => new StrictModuleResource($this->whenLoaded('module')),
            'customer_favorite_shops' => StrictCustomerFavoriteShopResource::collection($this->whenLoaded('customerFavoriteShops')),


        ];
    }
}
