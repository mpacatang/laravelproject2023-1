<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 */
class Subscriber extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];



    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'email' => ['required', 'email', 'unique:subscribers,email' . $extra_rule],
        ];
    }

}
