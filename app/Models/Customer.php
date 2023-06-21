<?php

namespace App\Models;

use App\Helpers\Auth\HasTokenLog;
use App\Helpers\Util\StringUtil;
use App\Rules\MobileNumberRule;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * @property mixed fcm_token
 * @property mixed|string password
 * @property mixed email
 * @property mixed name
 * @property string $avatar
 * @property Carbon|mixed|null $mobile_number_verified_at
 * @property Carbon|mixed|null $email_verified_at
 */
class Customer extends BaseModel
{
    use HasTokenLog;

    //===================== Defaults  ====================================//

    protected $guarded = ['password'];

    protected string $imageBaseLocation = 'customer_images/';
    protected string $modelImageKey = 'avatar';


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'bool'
    ];

    //===================== Rules  ====================================//

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


    public static function googleLoginRules(): array
    {
        return [
            'uid' => ['required'],
        ];
    }


    public static function registerRules(): array
    {
        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'mobile_number' => ['required', 'numeric', 'unique:customers,mobile_number', new MobileNumberRule()],
            'email' => ['nullable', 'email', 'unique:customers,email'],
            'password' => ['required'],
            'from_referral' => ['nullable', 'exists:customers,referral'],
            'fcm_token' => []
        ];
    }

    public static function registerRuleMessage(): array
    {
        return [
            'from_referral.exists' => 'This referral is not valid',
        ];
    }

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        $rules = [
            'active' => ['boolean'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'mobile_number' => ['unique:customers,mobile_number' . $extra_rule],
            'email' => ['unique:customers,email' . $extra_rule],
        ];

        if ($id == null) {
            $rules['password'] = [];
        }
        return $rules;
    }


    //======================= Getters ===========================================//


    public static function getFromMobileOrEmail($mobile_number, $email): Customer|null
    {
        return self::when($mobile_number != null, function ($q) use ($mobile_number) {
            return $q->where('mobile_number', $mobile_number);
        })->when($email != null, function ($q) use ($email) {
            return $q->where('email', $email);
        })->first();
    }


    public function setPassword($password)
    {
        $this->password = Hash::make($password ?? StringUtil::generateRandomString());
    }


//===================== Functionalities  ====================================//


    public
    function saveAvatarImage(
        Request $request,
        $key = 'avatar'
    ): bool {
        return parent::saveImageFromRequest($request, requestImageKey: $key);
    }

    public
    function deleteAvatar(): bool
    {
        return parent::removeImage();
    }

//===================== RelationShips  ====================================//

    public function wallet(): HasOne
    {
        return $this->hasOne(CustomerWallet::class);
    }

//===================== Boot Events  ====================================//

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->referral = StringUtil::generateReferral();
        });

        self::created(function ($model) {
            $customer_wallet = new CustomerWallet();
            $customer_wallet->customer_id = $model->id;
            $customer_loyalty_wallet = new CustomerLoyaltyWallet();
            $customer_loyalty_wallet->customer_id = $model->id;
            $customer_wallet->save();
            $customer_loyalty_wallet->save();
        });
    }
}
