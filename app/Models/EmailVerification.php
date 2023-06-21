<?php

namespace App\Models;

use App\Helpers\Util\StringUtil;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $email
 * @property mixed $verifiable_type
 * @property mixed $verifiable_id
 * @property mixed $token
 */
class EmailVerification extends BaseModel
{

    public static function createEmailToken($verifiable_type, $verifiable_id, $email)
    {
        $email_verification = new EmailVerification();
        $email_verification->verifiable_type = $verifiable_type;
        $email_verification->verifiable_id = $verifiable_id;
        $email_verification->email = $email;
        $email_verification->token = StringUtil::uniqueToken();
        $email_verification->save();
        return $email_verification->token;

    }


    public static function getEmailVerification($token): EmailVerification|null
    {
        return EmailVerification::where('token', $token)->first();

    }


}
