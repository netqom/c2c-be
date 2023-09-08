<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\{ChatUser,Message,Product};
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['chat_owner','product_id','has_offer'];

    /*** Please check this NOTE we add this because we need random string instead of auto incremented id ****/
    public $incrementing = false;
    
    /** Get chat_users that belongs to the chat **/
    public function chat_users(): HasMany
    {
        return $this->hasMany(ChatUser::class);
    }

    /** Get messages that belongs to the chat **/
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /** Get product that belongs to chat **/
    public function product(){
        return $this->belongsTo(Product::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($table)
        {
            $table->id = Str::random(20);
        });
    }

    /** Start Chat **/
    public function startChat($request)
    {
        $checkChatUserPresent = DB::table('chats as c')
            ->join('chat_users as cu', function ($join) use($request){
                $join->on('cu.chat_id', '=', 'c.id')
                     ->where('cu.user_id', '=', $request->seller_id)
                     ->where('cu.product_id', '=', $request->product_id);
            })
            ->join('chat_users as cu2', function ($join) use($request){
                $join->on('cu2.chat_id', '=', 'c.id')
                     ->where('cu2.user_id', '=', Auth::id())
                     ->where('cu2.product_id', '=', $request->product_id);
            })
            ->select('c.*')->first();
            
        if(empty($checkChatUserPresent)){
            $chat = $this->create(['chat_owner' => Auth::id() , 'product_id' => $request->product_id]);
            ChatUser::insert([
                ['user_id' => Auth::id() , 'chat_id' => $chat->id,'product_id' => $request->product_id, 'created_at' => now() ,'updated_at' => now() ],
                ['user_id' => $request->seller_id , 'chat_id' => $chat->id, 'product_id' => $request->product_id, 'created_at' => now(),'updated_at' => now()]
            ]);  
            if(!empty($request->send_offer)){
                $this->sendOffer($request,$chat->id);
            }
            return $chat->id;
        }     
        if(!empty($request->send_offer)){
            $this->sendOffer($request,$checkChatUserPresent->id);
        }
        return $checkChatUserPresent->id;
    }

    public function sendOffer($request,$chat_id){
        $this->where('id',$chat_id)->update(['has_offer' => 1]);
        Log::info('Send offer',array('date' => date('Y-m-d H:i:s')));
        Message::create(['product_id' => $request->product_id, 'chat_id' => $chat_id, 'content' => $request->send_offer['content'], 'sender_id' => Auth::id(), 'has_offer' => 1, 'offer_amt' => $request->send_offer['offer_amt'], 'offer_response' => 0, 'is_purchased' => 0,'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')]);
    }
}
