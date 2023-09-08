<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ChatUser,Chat,User,Product,Order,Category,Message};
use Illuminate\Support\Facades\{Lang,Validator,Hash};
use Auth;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public $user;
    public $product;
    public $order;
    public $category;
    public $chat;
    public $chatUser;
    public $chatMessage;

    public function __construct(ChatUser $chatUser, Chat $chat, Message $chatMessage,User $user, Product $product, Order $order, Category $category){
        $this->user    = $user;
        $this->product = $product;
        $this->order = $order;		
        $this->category = $category;      
        $this->chat = $chat;      
        $this->chatUser = $chatUser;      
        $this->chatMessage = $chatMessage;      
    }

    /** Start Chat *******/
    public function startChat(Request $request)
    {
        $checkChatUserPresent = $this->chat->startChat($request);
        return response()->json(['status' => true, 'message' => '', 'data' => $checkChatUserPresent]);
    }

    /** Get Chat Users List ***/
    public function getChatUserList(Request $request)
    {
        $data = $this->chatUser->getChatUserList($request);
        return response()->json(['status' => true, 'message' => Lang::get('application.chat.success') , 'data' => $data ]); 
    }

}
