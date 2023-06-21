<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatResource;
use App\Jobs\ProcessChatMessageStatusNotification;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class ChatMessageController extends Controller
{

    public function index(Request $request, $chatId): AnonymousResourceCollection
    {
        $messages = ChatMessage::where('chat_id', $chatId)->latest('sent_at')->paginate(8);
        return ChatMessageResource::collection($messages);
    }


    public function create(Request $request, $chatId): Application|ResponseFactory|Response|ChatMessageResource
    {
        $validatedData = $this->validate($request, ChatMessage::rules());
        $chat = Chat::findOrFail($chatId);
        $message = $chat->addMessage(Customer::class, $this->getUserId(), $validatedData);
        return $message ? new ChatMessageResource($message) : $this->errorResponse("Message not sent");
    }

    public function setAsSeen(Request $request, $chatId, $id): Application|ResponseFactory|Response|ChatMessageResource
    {
        $message = ChatMessage::findOrFail($id);
        $message->seen = true;
        $message->save();
        ProcessChatMessageStatusNotification::dispatch($message);
        return $this->successResponse();
    }
}

