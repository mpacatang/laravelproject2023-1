<?php

namespace App\Jobs;

use App\Helpers\Notification\FCMOption;
use App\Http\Resources\ChatMessageResource;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessChatMessageNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public ChatMessage $message;


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
        try {
        $chat = $this->message->chat;
        $participant_tokens = ChatParticipant::with('user', 'user.activeTokenLogs')
            ->where('chat_id', $chat->id)
            ->where('id', '!=', $this->message->chat_participant_id)
            ->get()
            ->pluck('user.activeTokenLogs.*.fcm_token')
            ->flatten()->toArray();

        $data = [
            'type' => FCMOption::$chat_message_notification_type,
            'message' => new ChatMessageResource($this->message->withoutRelations())
        ];


            $fcm = new FCMOption();
            $fcm->title = $chat->title;
            $fcm->body = $this->message->message;
            $fcm->tokens = $participant_tokens;
            $fcm->data = $data;
            $fcm->sendNotification();
        } catch (Exception $e) {
            Log::info('Making new directory');

            error_log($e->getMessage());
        }
    }
}
