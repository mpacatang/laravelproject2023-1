<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;

/**
 * @method static Builder active()
 * @property mixed $active
 * @property mixed $title
 * @property mixed $type
 * @property mixed $shops
 */
class Module extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $casts = [
        'active' => 'boolean'
    ];


    //===================== Rules  ====================================//


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'type' => ['required','unique:modules,type' . $extra_rule],
            'title' => ['required', 'unique:modules,title' . $extra_rule],
            'active' => ['boolean'],
        ];
    }

    //======================= Getters ===========================================//

    public function scopeActive($query){
        return $query->where('active', true);
    }

    //============================ Functions =========================================================//

    public function makeAllRelatedInactive()
    {
        foreach ($this->shops as $shop) {
            $shop->makeAllRelatedInactive();
            $shop->active = false;
            $shop->save();
        }
    }

    public function canStockEditable(): bool
    {
        return $this->type != 'food';
    }

    //===================== RelationShips  ====================================//


    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }


    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }





}
