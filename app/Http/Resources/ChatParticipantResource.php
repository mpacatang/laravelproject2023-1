<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method getUserType()
 * @property mixed $id
 */
class ChatParticipantResource extends JsonResource
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
            'user' => new ChatUserResource($this->whenLoaded('user')),
            'user_type' => $this->getUserType(),
            'chat' => new ChatResource($this->whenLoaded('chat')),
        ];
    }


}
