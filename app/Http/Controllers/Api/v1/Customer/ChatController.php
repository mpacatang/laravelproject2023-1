<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class ChatController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $chats = Chat::with([
            'latestMessage',
            'participants',
            'participants.user'
        ])->onlyMyChat(
            Customer::class,
            $this->getUserId()
        )->get();
        return ChatResource::collection($chats);
    }

    public function with(Request $request,): Application|ResponseFactory|Response|ChatResource
    {
        $user_type = $request->get('user_type');
        $id = $request->get('id');
        $chat = Chat::findWith($user_type, $id, Customer::class, $this->getUserId());
        if ($chat) {
            $chat->loadRelations();
            return new ChatResource($chat);
        }
        return $this->errorResponse("You can't chat with shop at the moment");
    }


    public function update(Request $request, $chatId): Application|ResponseFactory|Response
    {
        $chat = Chat::findOrFail($chatId);
        $validated_data = $this->validate($request, Chat::updateRule());
        $chat->fill($validated_data);
        $chat->save();
        return $this->successResponse("Chat saved");
    }


}

