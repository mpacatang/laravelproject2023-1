<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property string $image
 */
class HomeBanner extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected string $imageBaseLocation = 'home_banner_images/';

    protected $casts = [
        'active' => 'boolean',
    ];

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'shop_id' => ['required_without:product_id'],
            'product_id' => ['required_without:shop_id'],
            'active' => ['boolean'],
        ];
    }

    //===================== Functionalities  ====================================//




    //===================== RelationShips  ====================================//

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class,);
    }


}
