<?php

namespace App\Models;

use App\Jobs\ProcessChatMessageNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @method Builder onlyMyChat(string $userType, int $userId)
 * @property mixed $title
 */
class Chat extends BaseModel
{
    //===================== Defaults  ====================================//

    protected $guarded = [];


    protected array|string $manuallyRelations = ['messages', 'participants', 'participants.user'];


    //===================== Rules  ====================================//


    public static function rules(): array
    {
        return [
            'to_id' => ['required', 'numeric'],
            'to' => ['required', 'in:admin,seller,customer,delivery_boy']
        ];
    }


    public static function updateRule(): array
    {
        return [
            'title' => ['required'],
        ];
    }


    //===================== Functionalities  ====================================//

    public function scopeOnlyMyChat($query, $userType, $userId)
    {
        return $query->whereHas(
            'participants',
            function ($q) use ($userType, $userId) {
                $q->where(function ($q) use ($userType, $userId) {
                    $q->where('user_type', $userType)->where('user_id', $userId);
                });
            }
        );
    }

    public static function findWith($toUserTypeString, $toId, $fromUserType, $fromId): ?Chat
    {
        if ($toUserTypeString == 'shop') {
            $shop = Shop::with('owner')->findOrFail($toId);
            $owner = $shop->owner;
            if ($owner != null) {
                return Chat::createOrFind(Seller::class, $owner->id, $fromUserType, $fromId);
            }
            return null;
        }

        $toUserType = [
            'customer' => Customer::class,
            'shop' => Seller::class,
            'admin' => Admin::class,
            'seller' => Seller::class,
            'delivery_boy' => DeliveryBoy::class
        ][$toUserTypeString] ?? $toUserTypeString;

        return Chat::createOrFind($toUserType, $toId, $fromUserType, $fromId);
    }

    public function addMessage(string $userType, int $userId, array $validatedData): ChatMessage|null
    {
        $participant = ChatParticipant::where('chat_id', $this->id)
            ->where('user_type', $userType)
            ->where('user_id', $userId)
            ->first();

        if ($participant) {
            $chat_message = new ChatMessage([
                'message' => $validatedData['message'],
                'chat_participant_id' => $participant->id,
                'chat_id' => $this->id,
                'sent_at' => now()
            ]);

            $chat_message->saveImageFromRequest(request());

            $chat_message->type = $chat_message->image != null ? ChatMessage::$image_type : ChatMessage::$text_type;

            $chat_message->save();

            ProcessChatMessageNotification::dispatch($chat_message);

            return $chat_message;
        }
        return null;
    }


    public static function createOrFind($user1_type, $user1_id, $user2_type, $user2_id): Chat
    {
        $chat = Chat::with('participants')->whereHas(
            'participants',
            function ($q) use ($user2_id, $user2_type, $user1_id, $user1_type) {
                $q->where(function ($q) use ($user1_id, $user1_type) {
                    $q->where('user_type', $user1_type)->where('user_id', $user1_id);
                });
            }
        )->whereHas(
            'participants',
            function ($q) use ($user2_id, $user2_type, $user1_id, $user1_type) {
                $q->where(function ($q) use ($user2_id, $user2_type, $user1_id, $user1_type) {
                    $q->where('user_type', $user2_type)->where('user_id', $user2_id);
                });
            }
        )->first();

        if($chat){
            return $chat;
        }

//        if ($chats && count($chats) > 0) {
//            foreach ($chats as $chat) {
//                if ($chats->participants->count() == 2) {
//                    return $chat;
//                }
//            }
//        }
        return DB::transaction(function () use ($user2_id, $user2_type, $user1_id, $user1_type) {
            $chat = Chat::create(['title' => 'Support']);
            $chat->save();
            $participant1 = ChatParticipant::create([
                'user_type' => $user1_type,
                'user_id' => $user1_id,
                'chat_id' => $chat->id
            ]);
            $participant2 = ChatParticipant::create([
                'user_type' => $user2_type,
                'user_id' => $user2_id,
                'chat_id' => $chat->id
            ]);
            $participant1->save();
            $participant2->save();
            return $chat;
        });
    }

    //===================== RelationShips  ====================================//


    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latest('sent_at');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChatParticipant::class);
    }

}
