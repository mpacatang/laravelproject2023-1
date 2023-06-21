<?php

namespace App\Jobs;

use App\Helpers\Notification\FCMHelper;
use App\Helpers\Notification\FCMOption;
use App\Models\Notification;
use App\Models\TokenLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public ?Notification $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {

        try {
            $fcm = new FCMOption();
            $fcm->title = $this->notification->title;
            $fcm->body = $this->notification->body;
            $fcm->tokens = TokenLog::getUserFCMTokens($this->notification->notifiable_type, $this->notification->notifiable_id);
            $fcm->data = $this->notification->data;
            $fcm->sendNotification();
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}
