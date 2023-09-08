<?php

namespace App\Http\Controllers\Admin;

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
        $user = User::find($request->user_id);
        $queryObj = '';
        $chatIds = ChatUser::where('user_id', '=', $user->id)->pluck('chat_id');
        $search_keyword  = $request['search_keyword'] ? $request['search_keyword'] : '';

		if(count($chatIds) > 0){
			 $queryObj = ChatUser::whereIn('chat_users.chat_id', $chatIds)
					->where('chat_users.user_id', '!=',  $user->id)
					->with([
						'user' => function($query){
							return $query->select('id','name','image_path');
						},
						'product' => function($query){
							return $query->select('id','title','price', 'slug');
						}
					])
                    ->when($search_keyword != '', function ($q) use ($search_keyword) {
                        $q->whereHas('product', function ($query) use($search_keyword) {
                            return $query->select('id','title','price', 'slug')
                                ->where('title',  'like',  '%' . $search_keyword . '%');
                        });
                    });		
		}else{	
          $queryObj=ChatUser::where('user_id', 0);
        }
        $data = $this->prepareData($queryObj, $request);
        $usersHtml = view('admin.chat.partials.user-list', compact('data','user'))->render();
        if(count($data['data']) > 0){
            return response()->json(['status' => true, 'message' => Lang::get('application.chat.success') , 'data' => $usersHtml ]);     
        }
        return response()->json(['status' => false, 'message' => '' , 'data' => $usersHtml ]); 
    }

    public function getChatMessages(Request $request){
        $chatId = $request->chat_id;
        $user_id=$request->user_id;
        $order_colomn = isset($request['sort']['field']) ? $request['sort']['field'] : 'id';
        $order_type   = isset($request['sort']['sort']) ? $request['sort']['sort'] : 'desc';
        $queryObj=Message::with([
            'user' => function($query){
                return $query->select('id','name','image_path');
            },
            'message_files' => function($q){
                return $q->select('id','message_id','file_path');
            }
        ])->orderBy($order_colomn, $order_type)->where('chat_id',$chatId);
        $data = $this->prepareData($queryObj, $request);
        $data['data'] = $data['data']->reverse();
        $messagesHtml = view('admin.chat.partials.messages', compact('data','user_id'))->render();
        if(count($data['data']) > 0){
          return response()->json(['status' => true, 'message' => Lang::get('application.chat.success') , 'data' => $messagesHtml ]);   
        }
        return response()->json(['status' => false, 'message' => '' , 'data' => $messagesHtml ]);  
    }

    public function communicationThread(Request $request,$user_id)
    {   
        return view('admin.chat.communication_thread')->with(['user_id' => $user_id]);
    }

}