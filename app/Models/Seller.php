<?php

namespace App\Models;

use App\Helpers\Auth\HasTokenLog;
use App\Rules\MobileNumberRule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;

/**
 * @property mixed $fcm_token
 * @property mixed|string $password
 * @property mixed $email
 * @property mixed $name
 * @property string $avatar
 * @property mixed $is_owner
 * @property mixed $id
 * @property int $shop_id
 * @property Shop $shop
 */
class Seller extends BaseModel
{
    use HasTokenLog;

    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected string $imageBaseLocation = 'seller_images/';
    protected string $modelImageKey = 'avatar';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_owner' => 'boolean',
        'active' => 'boolean',

    ];

    public static function withAll(): Builder
    {
        return self::with(['role']);
    }

    //===================== Rules  ====================================//

    public static function googleLoginRules(): array
    {
        return [
            'uid' => ['required'],
        ];
    }

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'active' => ['boolean'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:sellers,email' . $extra_rule],
            'mobile_number' => ['required', 'unique:sellers,mobile_number' . $extra_rule],
            'password' => $id == null ? ['required'] : [],
            'is_owner' => ['boolean'],
            'bank_name' => [],
            'account_number' => [],
            'role_id' => ['required_if:is_owner,false'],
            'shop_id' => []
        ];
    }


    public static function loginRules(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule()],
            'password' => ['required'],
        ];
    }


    public static function sendOTPRule(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule()],
        ];
    }


    public static function verifyOTPRule(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule()],
            'otp' => ['required'],
        ];
    }

    public static function loginRuleMessage(): array
    {
        return [
            'email.exists' => 'This email is not exists',
            'email.required_without' => 'Enter a email address',
            'mobile_number.required_without' => 'Enter a mobile number',
        ];
    }


    //======================= Getters ===========================================//


    public static function getFromMobileOrEmail($mobile_number, $email): Seller|null
    {
        return self::when($mobile_number != null, function ($q) use ($mobile_number) {
            return $q->where('mobile_number', $mobile_number);
        })->when($email != null, function ($q) use ($email) {
            return $q->where('email', $email);
        })->first();
    }


    //===================== Functionalities  ====================================//


    public function saveAvatarImage(Request $request, $key = 'avatar'): bool
    {
        return parent::saveImageFromRequest($request, requestImageKey: $key);
    }

    public function removeAvatar(): bool
    {
        return parent::removeImage();
    }


    //===================== RelationShips  ====================================//

    public function role(): BelongsTo
    {
        return $this->belongsTo(SellerRole::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }


}
