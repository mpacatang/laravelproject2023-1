<?php

namespace App\Http\Resources;

use App\Models\ChatParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $title
 * @property mixed $id
 */
class ChatResource extends JsonResource
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
            'title' => $this->title,
            'participants' => ChatParticipantResource::collection($this->whenLoaded('participants')),
            'messages' => ChatMessageResource::collection($this->whenLoaded('messages')),
            'latest_message' => new ChatMessageResource($this->whenLoaded('latestMessage')),
        ];
    }


}
