<?php

namespace App\Models;

use App\Helpers\CResponse;
use App\Helpers\Util\StringUtil;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @property mixed $discount
 * @property mixed $discount_type
 * @property mixed $price
 * @property int $selling_count
 * @property mixed $product_variant_id
 * @property mixed|null $available_to
 * @property mixed|null $available_from
 * @property Module $module
 * @property mixed $module_id
 * @method $this active()
 */
class Product extends BaseModel
{
    //===================== Defaults  ====================================//
    protected $guarded = [];

    protected $casts = ['need_prescription' => 'boolean', 'active' => 'boolean',];

    //Food Types
    public static string $veg_type = 'veg';
    public static string $non_veg_type = 'non_veg';
    public static string $vegan_type = 'vegan';

    private static array $needRelations = [
        'unit',
        'productImages',
        'productOptions',
        'shop',
        'shop.module',
        'addons',
        'shop.timings',
        'subCategory',
        'category',
        'productVariant',
        'productVariant.products',
        'productVariant.products.productImages',
        'productVariant.products.productOptions',
        'productVariant.products.addons',
        'productVariant.products.subCategory',
        'productVariant.products.category',
    ];

    public static function withAll(): Builder|self
    {
        return static::with(static::$needRelations);
    }

    public function loadAll(): Product
    {
        return $this->load(static::$needRelations);
    }

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'name' => ['required'],
            'unit_id' => ['required_with:unit_title', Rule::exists('units', 'id')],
            'unit_title' => [],
            'product_variant_id' => [],
            'shop_id' => ['required'],
            'description' => [],
            'sub_category_id' => ['required', Rule::exists('sub_categories', 'id')],
            'active' => ['boolean'],
            'need_prescription' => ['boolean'],
            'food_type' => [],
            'available_from' => ['date_format:H:i',],
            'available_to' => ['required_with:available_from', 'date_format:H:i', 'after:available_from'],
        ];
    }

    public static function ruleMessages(): array
    {
        return ['available_to.required_with' => 'Set available to for the product also',];
    }

    //======================= Getters ===========================================//

    public static function extractFromData(array $validated_data): array
    {
        return [
            'shop_id' => $validated_data['shop_id'],
            'description' => $validated_data['description'] ?? null,
            'sub_category_id' => $validated_data['sub_category_id'],
            'name' => $validated_data['name'],
            'unit_id' => Arr::get($validated_data, 'unit_id'),
            'unit_title' => Arr::get($validated_data, 'unit_title'),
            'food_type' => $validated_data['food_type'] ?? null,
            'available_from' => $validated_data['available_from'] ?? null,
            'available_to' => $validated_data['available_to'] ?? null,
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }


    //===================== Functionalities  ====================================//

    public function saveImages(Request $request, $key = 'images'): bool
    {
        if ($request->has($key)) {
            $imageList = $request->get($key);
            if (is_array($imageList)) {
                foreach ($imageList as $image) {
                    ProductImage::saveProductImage($this, $image);
                }
            } else {
                ProductImage::saveProductImage($this, $imageList);
            }
        }

        return false;
    }


    public function updateAddons(array $ids)
    {
        $this->addons()->sync($ids);
    }


    //===================== Imports  ====================================//

    public static function getValidatedArrayFromXLSXRequest(Request $request): array|string
    {
        if ($request->hasFile('file')) {
            $v_data = $request->validate([
                'file' => 'required|mimes:xls,xlsx',
                'shop_id' => 'required'
            ]);

            try {
                $shop_id = $v_data['shop_id'];
                $data = Excel::toArray((object)[], $request->file('file'))[0];

                if (count($data) < 2) {
                    return "Xlsx sheet is empty. Fill data and try again";
                }
                $insert_data = [];
                foreach (array_slice($data, 1) as $row) {
                    $insert_data[] = [
                        'name' => $row[0],
                        'sub_category_id' => $row[1],
                        'price' => $row[2],
                        'stock' => $row[3],
                        'unit_id' => $row[4],
                        'unit_title' => $row[5],
                        'sku' => $row[6],
                        'barcode' => $row[7],
                        'image'=>$row[8],
                        'description'=>$row[9],
                        'shop_id' => $shop_id
                    ];
                }

                return Product::validateBulkData($insert_data, $shop_id);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            return "Xlsx file is missing";
        }
    }

    public static function validateBulkData(array $data, $shop_id): string|array
    {
        foreach ($data as $single) {
            if (empty($single['name'])) {
                return "Please enter product name properly";
            }
        }

        $module_id = Shop::findOrFail($shop_id)->module_id;
        foreach ($data as &$single) {
            if (empty($single['sub_category_id'])) {
                return "Please enter sub category id in: " . $single['name'];
            }
            if (empty($single['price'])) {
                return "Please enter price in: " . $single['name'];
            }
            $price = floatval($single['price']);
            if ($price <= 0) {
                return "Please enter price properly in: " . $single['name'];
            }
            $stock = $single['stock'];
            if (empty($stock) || (floatval($stock) <= 0)) {
                $single['stock'] = 100;
            }

            $sub_category = SubCategory::where('id', $single['sub_category_id'])
                ->whereHas('category', fn($q) => $q->where('module_id', $module_id))->first();


            if (!$sub_category) {
                return "Please enter sub category id valid in: " . $single['name'];
            }
            $single['sub_category'] = $sub_category;
            $single['category_id'] = $sub_category->category_id;
            $single['module_id'] = $module_id;


            $unit_id = $single['unit_id'];
            if ($unit_id) {
                $single['unit'] = Unit::where('id', $single['unit_id'])->first();
                $single['unit_id'] = $single['unit']?->id;
            }
        }
        return $data;
    }

    public static function uploadValidatedBulkData(array $data): Response|Application|ResponseFactory
    {
        try {
            DB::transaction(function () use ($data) {
                foreach ($data as $single) {
                    $product_variant = new ProductVariant();
                    $product_variant->save();
                    $data = collect($data);
                    $product = new Product([
                        'name' => $single['name'],
                        'product_variant_id' => $product_variant->id,
                        'sub_category_id' => $single['sub_category_id'],
                        'category_id' => $single['category_id'],
                        'unit_id' => $single['unit_id'],
                        'unit_title' => $single['unit_title'],
                        'shop_id' => $single['shop_id'],
                        'module_id' => $single['module_id'],
                        'description' => $single['description'],
                    ]);
                    $product->save();

                    $product_option = new ProductOption([
                        'product_id' => $product->id,
                        'stock' => $single['stock'],
                        'price' => $single['price'],
                        'sku' => $single['sku'],
                        'barcode' => $single['barcode'],
                    ]);
                    $url = $single['image'];
                    if($url) {
                        ProductImage::createFromURL($product,$url);
                    }
                    $product_option->save();
                }
            });
            return CResponse::success();
        } catch (Exception $e) {
            return CResponse::error($e->getMessage());
        }
    }


    //===================== RelationShips  ====================================//

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'product_addons', 'product_id', 'addon_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function customerFavoriteProducts(): HasMany
    {
        return $this->hasMany(CustomerFavoriteProduct::class, 'product_id', 'id');
    }


    public function productOptions(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public static function getFavoriteRelation($customer_id = null): array
    {
        if ($customer_id == null) {
            return [];
        }
        return [
            'customerFavoriteProducts' => function ($hasMany) use ($customer_id) {
                $hasMany->where('customer_id', $customer_id);
            }
        ];
    }


    //=====================  Boot Events  ====================================//

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->slug = StringUtil::getSlugFromText($model->name);
        });
    }
}
