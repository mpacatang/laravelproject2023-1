<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 */
class ShopTime extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    public static array $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');


    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'shop_id' => ['required'],
            'day' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'open_at' => ['required', 'date_format:G:i'],
            'close_at' => ['required', 'date_format:G:i', 'after:open_at'],
        ];
    }

}
