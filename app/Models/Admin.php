<?php

namespace App\Models;

use App\Helpers\Auth\HasTokenLog;
use App\Rules\MobileNumberRule;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property mixed $fcm_token
 * @property mixed|string $password
 * @property mixed $email
 * @property mixed $name
 * @property mixed $avatar
 * @method static getAll()
 */
class Admin extends BaseModel
{
    use HasTokenLog;

    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected string $imageBaseLocation = 'admin_images/';
    protected string $modelImageKey = 'avatar';


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_owner' => 'boolean',

    ];

    public static function withAll(): Builder
    {
        return static::with(['role']);
    }

    //===================== Rules  ====================================//

    public static function loginRules(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule()],
            'password' => ['required'],
        ];
    }


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'active' => ['boolean'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:admins,email' . $extra_rule],
            'mobile_number' => ['required', 'unique:admins,mobile_number' . $extra_rule, new MobileNumberRule()],
            'password' => $id == null ? ['required'] : [],
            'is_super' => ['boolean'],
            'role_id' => ['required_if:is_super,false'],

        ];
    }


    public static function googleLoginRules(): array
    {
        return [
            'uid' => ['required'],
        ];
    }



    //======================= Getters ===========================================//

    public static  function getFromMobileOrEmail($mobile_number, $email): Admin|null
    {
        return self::when($mobile_number != null, function ($q) use ($mobile_number) {
            return $q->where('mobile_number', $mobile_number);
        })->when($email != null, function ($q) use ($email) {
            return $q->where('email', $email);
        })->first();
    }


    //===================== Functionalities  ====================================//

    public function saveAvatarImage(Request $request): bool
    {
        return parent::saveImageFromRequest($request, requestImageKey: 'avatar');
    }

    public function removeAvatar(): bool
    {
        return parent::removeImage();
    }


    //===================== RelationShips  ====================================//

    public function role(): BelongsTo
    {
        return $this->belongsTo(AdminRole::class,);
    }

}
