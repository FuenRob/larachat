<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Events\NewChatMessage;

class ChatController extends Controller
{
    public function rooms(Request $request) {
        return ChatRoom::all();
    }

    public function messages(Request $request, $roomId) {
        return ChatMessage::where('chat_room_id', $roomId)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function newMessage(Request $request, $roomId) {
        $newChatMessage = new ChatMessage();
        $newChatMessage->user_id = Auth::id();
        $newChatMessage->chat_room_id = $roomId;
        $newChatMessage->message = $request->message;
        $newChatMessage->save();

        broadcast(new NewChatMessage($newChatMessage))->toOthers();

        return $newChatMessage;
    }
}
