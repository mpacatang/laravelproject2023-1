<?php

namespace App\Http\Resources;

use App\Helpers\Util\ImageUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $chat_participant_id
 * @property mixed $chat_id
 * @property mixed $seen
 * @property mixed $image
 * @property mixed $message
 * @property mixed $type
 * @property mixed $id
 * @property mixed $sent_at
 */
class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */


    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'message' => $this->message,
            'image' => ResourceUtil::getImagePath($this->image),
            'seen' => $this->seen,
            'chat_id' => $this->chat_id,
            'chat_participant_id' => $this->chat_participant_id,
            'sent_at' => $this->sent_at,
            'chat' => new ChatResource($this->whenLoaded('chat')),
            'participant' => new ChatParticipantResource($this->whenLoaded('participant')),
        ];
    }


}
