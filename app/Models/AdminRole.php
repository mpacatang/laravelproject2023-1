<?php

namespace App\Models;

use Exception;

/**
 * @property false|mixed|string $permissions
 */
class AdminRole extends BaseModel
{

    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'title' => ['required'],
            'active' => ['boolean'],
            'permissions' => ['required', 'array'],
        ];
    }

    public static function ruleMessages($id = null): array
    {
        return [
            'permissions.required' => 'Select at least one permission',
        ];
    }

    //===================== Functionalities  ====================================//

    public function hasPermission($permission): bool
    {
        try {
            $permissions = json_decode($this->permissions);
            return in_array($permission, $permissions);
        } catch (Exception $e) {
        }
        return false;
    }
}
