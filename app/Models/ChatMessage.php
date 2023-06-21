<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method paginate(?int $pageSize = 15)
 * @method latest(string $orderBy)
 * @property int $chat_id
 * @property Chat $chat
 * @property string $message
 * @property int $chat_participant_id
 * @property string $type
 * @property string $image
 * @property mixed|true $seen
 */

class ChatMessage extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected string $modelImageKey = 'image';
    protected string $imageBaseLocation = 'chat_images/';


    protected $casts = [
        'seen' => 'boolean',
        'sent_at' => 'datetime',
    ];


    public static string $text_type = 'text';
    public static string $image_type = 'image';


    //===================== Rules  ====================================//


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'message' => ['required_without:image'],
            'image' => ['required_without:message'],
        ];
    }


    //===================== Functionalities  ====================================//


    //===================== RelationShips  ====================================//


    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(ChatParticipant::class);
    }

}
