<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{User,Notification,MessageFile};
use Auth,Storage;

class Message extends Model
{
    use HasFactory;

    //public $timestamps = true;

    
    protected $fillable = ['chat_id','sender_id','product_id','content','is_purchased','has_offer','offer_amt'];

    /** Get user that belongs to chat users **/
    public function user(){
        return $this->belongsTo(User::class,'sender_id','id');
    }

    /** Get image that belongs to chat message **/
    public function message_files(){
        return $this->hasOne(MessageFile::class, 'message_id', 'id');
    }



    /** Get Messages For Particular Chat **/
    // public function retrieveMessage($request){
	// 	return $this->with([
    //         'user' =>function($queryUser){
    //             return $queryUser->select('id','name','image_path');
    //         }
    //     ])->where(['chat_id' => $request->chat_id])->orderBy('id', 'desc');
    // }
    
    /** Sent Messages For Particular Chat **/
    // public function sendMessage($request){


    //     if($messageSave = $this->create($request->all())){
    //         $file = $request->file_data;
    //         if($file){                
    //             $destinationPath = storage_path('app/public/chat');
    //             $file_type = 1;
    //             if (str_contains($file['type'], 'video')) { 
    //                $file_type = 2;
    //             }
    //             $image_parts = explode(";base64,", $file['binary']);
    //             $image_type_aux = explode("image/", $image_parts[0]);
    //             $image_type = $image_type_aux[1];
    //             $image_base64 = base64_decode($image_parts[1]);
    //             if(\File::put($destinationPath.'/'.$messageSave->id.'_'.$file['name'],$image_base64)){
    //                 MessageFile::create([
    //                     'message_id' => $messageSave->id,
    //                     'file_type'  => $file_type,
    //                     'file_path'  => url('storage/chat/'.$messageSave->id.'_'.$file['name']),
    //                     'mime_type'  => $file['type']
    //                 ]);
    //             }
    //         }

    //         /**Notify **/
    //         foreach ($request->notify_users as $chatKey => $chatValue) {
    //             $notification = new Notification();
    //             $notification->user_id     = $chatValue;
    //             $notification->type        = 4;
    //             $notification->item_id     = $request->chat_id;
    //             $notification->description = "You have recieved a new message from ".Auth::user()->name." to chat with this user <a href='".env("FRONTEND_APP_URL")."/auth/start-chat/".$request->chat_id."'>Click Here</a>";
    //             $notification->status      = 1;
    //             $notification->created_by  = Auth::id();
    //             $notification->updated_by  = Auth::id();
    //             $notification->save();
    //         }
            
    //     }        
    //     return $this->with([
    //         'user' =>function($queryUser){
    //             return $queryUser->select('id','name','image_path');
    //         }
    //     ])->where(['chat_id' => $request->chat_id])->latest()->take(3);
    // }

}
