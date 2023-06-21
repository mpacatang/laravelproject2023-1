<?php

namespace App\Models;

use App\Jobs\ProcessRawSMS;
use App\Mail\OTPMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\Mail;


/**
 * @property mixed $email
 * @property mixed $mobile_number
 * @property mixed $verifiable_type
 * @property mixed $verifiable_id
 * @property mixed $otp
 */
class OtpVerification extends BaseModel
{

    public static function createOTPTokenByEmail($verifiable_type, $verifiable_id, $email): OtpVerification
    {
        $otp_verification = new OtpVerification();
        $otp_verification->verifiable_type = $verifiable_type;
        $otp_verification->verifiable_id = $verifiable_id;
        $otp_verification->email = $email;
        $otp_verification->otp = rand(100000, 999999);
        $otp_verification->save();
        return $otp_verification;
    }

    public static function createOTPTokenByMobile($verifiable_type, $verifiable_id, $mobile_number): OtpVerification
    {
        $otp_verification = new OtpVerification();
        $otp_verification->verifiable_id = $verifiable_id;
        $otp_verification->verifiable_type = $verifiable_type;
        $otp_verification->mobile_number = $mobile_number;
        $otp_verification->otp = rand(100000, 999999);
        $otp_verification->save();
        return $otp_verification;

    }

    public static function getOTPVerificationByOTP($verifiable_type, $verifiable_id, $otp): ?OtpVerification
    {
        return OtpVerification::where('verifiable_id', $verifiable_id)
            ->where('verifiable_type', $verifiable_type)
            ->where('otp', $otp)->first();
    }


    public function sendViaEmail(): ?SentMessage
    {
        return Mail::to($this->email)->send(new OTPMail($this->otp));
    }

    public function sendViaSMS(): bool
    {
        $message = "Your otp is: " . $this->otp;
        ProcessRawSMS::dispatch($this->mobile_number, $message);
        return true;

    }


}
