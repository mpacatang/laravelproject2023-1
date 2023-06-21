<?php

namespace App\Http\Resources\Strict;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductImageResource;
use App\Http\Resources\ResourceUtil;
use App\Http\Resources\SubCategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @property mixed $ratings_count
 * @property mixed $ratings_total
 * @property mixed $sub_category_id
 * @property mixed $shop_id
 * @property mixed $description
 * @property mixed $name
 * @property mixed $addon
 * @property mixed $active
 * @property mixed $id
 * @property mixed $available_to
 * @property mixed $available_from
 * @property mixed $need_prescription
 * @property mixed $unit_title
 * @property mixed $unit_id
 * @property mixed $product_id
 * @property mixed $option_title
 * @property mixed $option_type
 * @property mixed $product_variant_id
 * @property mixed $slug
 * @property mixed $rating
 * @property mixed $food_type
 * @property mixed $category_id
 * @property mixed $selling_count
 */
class StrictProductResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'active' => $this->active,
            'need_prescription' => $this->need_prescription,
            'addon' => $this->addon,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'ratings_total' => $this->ratings_total,
            'ratings_count' => $this->ratings_count,
            'selling_count' => $this->selling_count,
            'rating' => $this->rating,
            'food_type' => $this->food_type,
            'available_from' => ResourceUtil::getFormattedTime($this->available_from),
            'available_to' => ResourceUtil::getFormattedTime($this->available_to),
            'product_id' => $this->product_id,
            'unit_id' => $this->unit_id,
            'unit_title' => $this->unit_title,
            'unit' => new StrictUnitResource($this->whenLoaded('unit')),
            'shop_id' => $this->shop_id,
            'product_variant_id' => $this->product_variant_id,
            'sub_category_id' => $this->sub_category_id,
            'category_id' => $this->category_id,
            'variant' => new StrictProductVariantResource($this->whenLoaded('productVariant')),
            'shop' => new StrictShopResource($this->whenLoaded('shop')),
            'sub_category' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'addons' => StrictAddonResource::collection($this->whenLoaded('addons')),
            'images' => ProductImageResource::collection($this->whenLoaded('productImages')),
            'options' => StrictProductOptionResource::collection($this->whenLoaded('productOptions')),
            'customer_favorite_products' => StrictCustomerFavoriteProductResource::collection($this->whenLoaded('customerFavoriteProducts')),
            'option_type' => $this->option_type,
            'option_title' => $this->option_title,

        ];


    }


}
