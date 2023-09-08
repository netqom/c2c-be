<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use App\Mail\SendMail;
use Mail;
use App\Models\SupportTicket;

class SupportTicketMessage extends Model
{
    use HasFactory;

    protected $fillable = ['unique_ticket','message','sender_id','receiver_id'];

    protected $appends = ['display_message_file'];

    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function support_ticket_file(){
        return $this->hasOne(SupportTicketFile::class);
    }

    public function support_ticket(){
        return $this->hasOne(SupportTicket::class, 'unique_ticket','unique_ticket')->where('deleted_at', null);
    }

    public function getDisplayMessageFileAttribute() 
    {
        if($this->file_path != ''){
            return url('storage/'.$this->file_path);
        }
        return ''; 
    }

    /** Get Chat For To Specific Ticket **/
    public function getChatForTicket($request)
    {
        $unique_ticket = $request['query']['unique_ticket'];
        $order_colomn = isset($request['sort']['field']) ? $request['sort']['field'] : 'id';
        $sort_type   = isset($request['sort']['sort']) ? $request['sort']['sort'] : 'desc';
        $query = $this->select('id','unique_ticket','message','sender_id','receiver_id','created_at','updated_at');
        $query->with(['sender' => function($senderQu){
                return $senderQu->select('id','name','email','image_path');
            },
            'receiver'=> function($receiverQu){
                return $receiverQu->select('id','name','email','image_path');
            },
            'support_ticket_file' => function($supportTicketFileQu) {
                return $supportTicketFileQu->select('id','support_ticket_message_id','file_path');
            }
        ])->where('unique_ticket',$unique_ticket);
        return $query->orderBy($order_colomn, $sort_type);  
    }

    public function sendMessage($request)
    {
        $receiver_detail = User::where('id',$request->receiver_id)->select('id','name','email')->first();
        $sender_detail = User::where('id',Auth::user()->id)->select('id','name','email')->first(); 
        $saveMessage = $this->create([
            'unique_ticket' => $request->unique_ticket,
            'message' => $request->message,
            'sender_id' => Auth::user()->id,
            'receiver_id' => (int) $request->receiver_id,
        ]);
        if($saveMessage){
            if(Auth::user()->id != 1){
                $url = env('APP_URL')."/admin/help-center/ticket-threads/".$request->unique_ticket;
            }else{
                $url = env('FRONTEND_APP_URL')."/auth/ticket-threads/".$request->unique_ticket;
            }
            $thread_update = "You have a new update on <a style='color: #0d6efd; font-size: 15px; text-decoration: none;' href=".$url.">".$request->unique_ticket." </a> as link";
            if(isset($request->new_ticket)){
                $thread_update = "";
            }
            if($request->subject){
                $title = $request->subject;
            }else{
                $supTickData = SupportTicket::where('unique_ticket',$request->uniqid)->first();
                $title = $supTickData['title'];
            }
            
            $body = "<h4>Hi ".$receiver_detail->name."</h4><p>".$thread_update."</p><p><strong>Title: </strong>".$title."</p><p><strong>Description: </strong>".$request->message."</p><br><br>
            Thank you.";
            $subject = "Thread Update from ".$sender_detail->name." on ".$request->unique_ticket;
            if(isset($request->new_ticket)){
                $subject = "New Thread created by ".$sender_detail->name." as ".$request->unique_ticket;
            }
           
            Mail::send('emails.ticketChatMessage', ['content' => $body], function($message) use($request,$subject,$receiver_detail){
            $message->to($receiver_detail->email);
            $message->subject($subject);
            });
            return $saveMessage;
        }
        return '';
    }
}
