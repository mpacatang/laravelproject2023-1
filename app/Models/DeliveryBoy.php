<?php

namespace App\Models;

use App\Helpers\Auth\HasTokenLog;
use App\Rules\MobileNumberRule;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @property string $avatar
 * @property string $identity_image
 * @property mixed $shop_id
 * @property false|mixed $archived
 * @property bool|mixed $approved
 * @property false|mixed $active
 * @property int|mixed $ratings_count
 * @property mixed $ratings_total
 * @property mixed $rating
 * @property mixed $id
 * @property mixed $salary_based
 * @property mixed $password
 */
class DeliveryBoy extends BaseModel
{
    use HasTokenLog;

    // --------------------------- Defaults ---------------------------------------//


    protected $guarded = [];

    protected $casts = [
        'active_for_delivery' => 'boolean',
        'salary_based' => 'boolean',
        'active' => 'boolean',
        'approved' => 'boolean',
        'archived' => 'boolean',
    ];


    // --------------------------- Rules ---------------------------------------//


    public static function loginRules(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule()],
            'password' => ['required'],
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

    public static function googleLoginRules(): array
    {
        return [
            'uid' => ['required'],
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


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        $rule = [
            'active' => ['boolean'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:delivery_boys,email' . $extra_rule],
            'vehicle_number' => ['required'],
            'mobile_number' => ['required', 'unique:delivery_boys,mobile_number' . $extra_rule],
            'identity_type' => ['required', 'in:passport,driving_license,other'],
            'identity_number' => ['required'],
            'bank_name' => [],
            'account_number' => [],
            'salary_based' => ['boolean'],

            'shop_id' => ['nullable', Rule::exists('shops', 'id')],
            'zone_id' => ['nullable', 'required_without:shop_id', Rule::exists('shops', 'id')],
        ];

        if ($id == null) {
            $rule['password'] = ['required'];
        }

        return $rule;
    }


    //======================= Getters ===========================================//


    public static function getFromMobileOrEmail($mobile_number, $email): ?DeliveryBoy
    {
        return self::when($mobile_number != null, function ($q) use ($mobile_number) {
            return $q->where('mobile_number', $mobile_number);
        })->when($email != null, function ($q) use ($email) {
            return $q->where('email', $email);
        })->first();
    }


    // ------------------------- Functionalities ---------------------------------------------------//


    public function saveAvatarImage(Request $request, $key = 'avatar'): bool
    {
        return parent::saveImageFromRequest(
            $request,
            imageBaseLocation: 'delivery_images/',
            modelKey: 'avatar',
            requestImageKey: $key
        );
    }

    public function saveIdentityImage(Request $request, $key = 'identity_image'): bool
    {
        return parent::saveImageFromRequest(
            $request,
            imageBaseLocation: 'delivery_identity_images/',
            modelKey: 'identity_image',
            requestImageKey: $key
        );
    }

    public function removeAvatar()
    {
        parent::removeImage(modelKey: 'avatar');
    }

    public function removeIdentityImage()
    {
        parent::removeImage(modelKey: 'identity_image');
    }


    // ------------------------- Relationships ---------------------------------------------------//

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

}
