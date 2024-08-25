<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chatroom;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function getChat(Chatroom $chatroom){
        $messages = Message::with('user')->where('chatroom_id', $chatroom->id)->get();

        return response()->json([
            'status' => 200,
            'messages' => $messages
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'chatroom_id' => 'required|exists:chatrooms,id',
            'message' => 'required'
        ]);
        

        $data = [
            'chatroom_id' => $request->chatroom_id,
            'user_id' => $request->user()->id,
            'message' => $request->message
        ];

        $res = Message::create($data);

        return response()->json([
            'status' => 200,
            'message' => $res
        ]);
    }
}
