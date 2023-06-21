<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $price
 * @property mixed $image
 * @property mixed $shop_id
 */
class Addon extends BaseModel
{

    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected string $imageBaseLocation = "addon_images/";

    protected $casts = [
        'active' => 'boolean',
    ];

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'shop_id' => ['required'],
            'name' => ['required'],
            'description' => ['nullable'],
            'price' => ['required', 'numeric', 'min:0'],
            'active' => ['boolean']
        ];
    }

    //===================== Functionalities  ====================================//


    //===================== RelationShips  ====================================//

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }


}
