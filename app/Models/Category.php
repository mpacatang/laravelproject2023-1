<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property bool|mixed|string $image
 * @property mixed $active
 */
class Category extends BaseModel
{
    use HasImage;
    use HasFactory;

    protected string $imageBaseLocation = 'category_images/';

    // --------------------------- Defaults ---------------------------------------//

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    // --------------------------- Rules ---------------------------------------//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'name' => ['required'],
            'description' => [],
            'active' => ['boolean'],
            'module_id' => ['required']
        ];
    }


    // ------------------------- Relationships ---------------------------------------------------//
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // ------------------------- Life Cycle ---------------------------------------------------//

    public function update(array $attributes = [], array $options = []): bool
    {
        $active = $attributes['active'];
        if (isset($active) && !$active && $this->active) {
            $this->subCategories()->update(['active' => false]);
            $this->products()->update(['active' => false]);
        }
        return parent::update($attributes, $options);
    }


}
