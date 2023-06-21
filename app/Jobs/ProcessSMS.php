<?php

namespace App\Jobs;

use App\Models\BusinessSetting;
use App\Models\ShortMessage;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class ProcessSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ShortMessage $message;


    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (BusinessSetting::_get(BusinessSetting::$sms_twilio_enable)) {
            $user = $this->message->messageable()->first();

            if (!isset($user) || !isset($user->mobile_number)) {
                return;
            }
            $to = $user->mobile_number;
            $from =  BusinessSetting::_get(BusinessSetting::$sms_twilio_mobile_number);
            $message = $this->message->message;
            try {
                $sid = BusinessSetting::_get(BusinessSetting::$sms_twilio_sid);
                $token = BusinessSetting::_get(BusinessSetting::$sms_twilio_token);
                $client = new Client($sid, $token);


                $client->messages->create(
                    $to,
                    [
                        'from' => $from,
                        'body' => $message
                    ]
                );

                error_log($from . " " . $to . " " . $message);
            } catch (Exception $e) {

            }
        }
    }
}
