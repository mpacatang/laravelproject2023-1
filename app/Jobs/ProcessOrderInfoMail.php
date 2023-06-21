<?php

namespace App\Jobs;

//use App\Helpers\Notification\FCMHelper;
use App\Mail\OrderInfoMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessOrderInfoMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public ?Order $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $email = $this->order->customer->email;
        if (isset($email)) {
            Mail::to($email)->send(new OrderInfoMail($this->order));
        }
//        try {
//            $fcm = new FCMOption();
//            $fcm->title = $this->notification->title;
//            $fcm->body = $this->notification->body;
//            $fcm->tokens = TokenLog::getUserFCMTokens($this->notification->notifiable_type, $this->notification->notifiable_id);
//            $fcm->data = $this->notification->data;
//            FCMHelper::sendNotification($fcm);
//        } catch (Exception $e) {
//            error_log($e->getMessage());
//        }
    }
}
