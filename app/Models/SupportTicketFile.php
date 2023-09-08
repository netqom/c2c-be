<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth,File,Storage;

class SupportTicketFile extends Model
{
    use HasFactory;

    protected $fillable = ['id','support_ticket_id','file_path','file_type','created_at','updated_at'];

    public function saveMessageFile($request,$saveTicket){
        $files = $request->file('file');
        $uploadImage = false;
        $data = [];
        foreach($files as $key => $file){
            $extension  = $file->getClientOriginalExtension();
            $image_name = date('mdYHis') . uniqid(). '.' .$extension;
            Storage::disk('public')->putFileAs('tickets/'.$request->unique_ticket.'/', $file, $image_name);
            $uploadImage = true;
            $data[$key]=[
                'support_ticket_message_id' => $saveTicket->id,
                'file_path' => 'tickets/'.$request->unique_ticket.'/'.$image_name,
                'file_type' => 'image',
            ];     
        }
        if($uploadImage){
            $this->insert($data);
        }
        return true;
    }

}
