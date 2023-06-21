<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 */
class Unit extends BaseModel
{
    //===================== Defaults  ====================================//
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean'
    ];

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'type' => ['required'],
            'title' => ['required', 'unique:units,title'.$extra_rule],
            'description' => ['nullable'],
            'active' => ['boolean']
        ];
    }
}
