<?php

namespace App\Jobs;

use App\Models\BusinessSetting;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Twilio\Rest\Client;

class ProcessRawSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $to, $message;


    public function __construct($to, $message)
    {
        $this->to = $to;
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
            $to = $this->to;
            $from =  BusinessSetting::_get(BusinessSetting::$sms_twilio_mobile_number);
            $message = $this->message;

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
                error_log($e->getMessage());
            }
        }
    }
}
