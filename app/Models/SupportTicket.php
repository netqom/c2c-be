<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use App\Models\{SupportTicketMessage,SupportTicketFile};
use Auth;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['subject','description','created_by','updated_by','status','unique_ticket','product_id'];

    public function product(){
        return $this->hasOne(Product::class, 'id','product_id')->withTrashed();
    }


    /** Get User for the product **/
    public function users()
    {
        return $this->belongsTo('App\Models\User','created_by');
    }

    /** Save Ticket From This Function **/
    public function saveTicket($request){

        $createTicket = $this->create([
            'subject' => $request->subject,
            'product_id' => $request->product_id ?? '',
            'description' => $request->description,
            'unique_ticket' => generateRandomString(15),
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id
        ]);
        if($createTicket){
            return $createTicket;
        }
        return '';
    }

    /** With Pagination Get List Of Tickets **/
    public function listOfTickets($request)
    {
        $search       = isset($request['query']['search_string']) ? $request['query']['search_string'] : '';
        $order_colomn = isset($request['sort']['field']) ? $request['sort']['field'] : 'id';
        $sort_type   = isset($request['sort']['sort']) ? $request['sort']['sort'] : 'desc';

        $query = $this->with(['users' => function($userQuery){
            return $userQuery->select('id','email');
        }])->select('id','subject','description','created_by','updated_by','status','unique_ticket','created_at'); 

        $query->when($search != '', function ($q) use ($search) {

            $q->where(function ($query)use ($search) {
                $query->where('subject',  'like',  '%' . $search . '%')
                    ->orWhere('unique_ticket',  'like',  '%' . $search . '%')
                    ->orWhereHas('users', function ($query2) use ($search) {
                        $query2->where('email',  'like',  '%' . $search . '%');
                    });
            });
           
        }); 

        if(Auth::id() != 1){
         $query->where('created_by', Auth::id());
        }
        
        return $query->orderBy($order_colomn, $sort_type);  
    }


    /** Delete Threads **/
    public function deleteItem($id)
    {
        $product = $this->find($id);
        $product->delete();
        return true;
    }
}
