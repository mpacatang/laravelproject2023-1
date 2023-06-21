<?php

namespace App\Jobs;

use App\Helpers\Notification\FCMOption;
use App\Http\Resources\ResourceUtil;
use App\Models\ManualNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessManualNotification implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public int $manual_notification_id;

    public function __construct($manual_notification_id)
    {
        $this->manual_notification_id = $manual_notification_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $notification = ManualNotification::find($this->manual_notification_id);
        if ($notification) {
            try {
                $fcm = new FCMOption();
                $fcm->title = $notification->title;
                $fcm->body = $notification->body;
                $fcm->tokens = $notification->getAllTokens();
                $data = $notification->data ?? [];
                if ($notification->actions) {
                    $data['actions'] = $notification->actions;
                }
                if ($notification->image != null) {
                    $data['image'] = ResourceUtil::getImagePath($notification->image);
                }
                $fcm->data = $data;
                $fcm->sendNotification();
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }
    }

    public function uniqueId(): int
    {
        return $this->manual_notification_id;
    }
}
