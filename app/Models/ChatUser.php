<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{User,Product,Message,Chat};
use DB,Auth;

class ChatUser extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id','user_id','product_id','has_offer'];

    /** Get chat that belongs to chat users **/
    public function chat(){
        return $this->belongsTo(Chat::class,'chat_id','id');
    }
    
    /** Get user that belongs to chat users **/
    public function user(){
        return $this->belongsTo(User::class,'user_id','id')->withTrashed(); // with thrashed use for chat purpose if user soft delete then also get result 
    }
	
    /** Get product that belongs to the chat  users **/
	public function product(){
        return $this->belongsTo(Product::class)->withTrashed();  // with thrashed use for chat purpose if product soft delete then also get result 
    }

    /** Get messages that belongs to the chat users **/
    public function messages(){
        return $this->hasMany(Message::class,'chat_id','chat_id');
    }

    /** Get Chat User List ***/
    public function getChatUserList($request)
    {       
        $chatIds = $this->where('user_id', '=', Auth::id())->pluck('chat_id');
		if(count($chatIds) > 0){
			return $this->whereIn('chat_users.chat_id', $chatIds)
					->where('chat_users.user_id', '!=', Auth::id())
					->with([
						'user' => function($query){
							return $query->select('id','name','image_path');
						},
						'product' => function($query){
							return $query->select('id','title','price', 'slug','created_by','quantity','deleted_at');
						},
						'messages' => function ($query) {
							return $query->select('id','created_at','chat_id','content')->latest()->first();
						}
					])
					->get();		
		}	
        return $this->where('user_id', 0)->get();
    }
	
	
}
