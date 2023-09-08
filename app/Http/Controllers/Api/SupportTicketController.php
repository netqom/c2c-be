<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{SupportTicket,SupportTicketMessage,SupportTicketFile,Order};
use Illuminate\Support\Facades\{Lang,Validator};
use Auth,File,Storage;

class SupportTicketController extends Controller
{
    public $supportTicket;
    public $supportTicketMessage;
    public $supportTicketFile;
    public $order;

    public function __construct(SupportTicket  $supportTicket, SupportTicketMessage $supportTicketMessage,SupportTicketFile $supportTicketFile,Order $order){
        $this->supportTicket = $supportTicket; 
        $this->supportTicketMessage = $supportTicketMessage; 
        $this->supportTicketFile = $supportTicketFile; 
        $this->order =  $order;
    }

    /*** Send Message Function ****/
    public function sendMessage(Request $request)
    {
        $request->request->add(['receiver_id' => 1]);
        $saveMessage = $this->supportTicketMessage->sendMessage($request);
        if ($request->hasFile('file')) {
           $this->supportTicketFile->saveMessageFile($request,$saveMessage);
        }
        if($saveMessage != ''){
           return response()->json(['status' => true, 'message' => '' ]); 
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong' ]);  
    }

    /** Save Ticket Form **/
    public function saveTicket(Request $request)
    {
        $conditions = ['subject' => ['required'],'description' => ['required'] ];
        $validator = Validator::make($request->all(), $conditions);         
        if ($validator->fails()) {
            return response()->json(['status' => false,  'message' => $validator->errors()->first()]);
        } 
        $saveTicket = $this->supportTicket->saveTicket($request);
        if($saveTicket != ''){
            $request->request->add(['message' => $request->description,'receiver_id' => 1,'unique_ticket' => $saveTicket->unique_ticket,'subject' => $request->subject, 'new_ticket' => true ]);
            $saveMessage = $this->supportTicketMessage->sendMessage($request);
            if ($request->hasFile('file')) {
                $this->supportTicketFile->saveMessageFile($request,$saveMessage);
            }
           return response()->json(['status' => true, 'message' => 'Ticket created successfully.' ]); 
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong' ]);  
    }

    /** List Of Tickets According To LoggedIn User  ***/
    public function listOfTickets(Request $request){
        if(Auth::check()){
            $queryObj = $this->supportTicket->listOfTickets($request);
            $data = $this->prepareData($queryObj, $request);
            return response()->json($data);
        }
        return response()->json([]);
    }

    /** Delete Product And Their Images **/
    public function deleteTicket(Request $request)
    {
        $result = $this->supportTicket->deleteItem($request->item_id);
        if ($result) {
            return response()->json(['status' => true, 'message' => Lang::get('Ticket removed successfully')]);
        } else {
            return response()->json(['status' => false, 'message' => Lang::get('auth.someError') ]);
        }
    }

    /******* Get Chat For Ticket *********/
    public function getChatForTicket(Request $request)
    {
        $queryObj = $this->supportTicketMessage->getChatForTicket($request);
        $data = $this->prepareData($queryObj, $request);
        $data['support_ticket'] = $this->supportTicket
            ->select('id','product_id','subject','description')
            ->with([
                'product' => function($q){
                    return $q->with([
                        'users' => function($u){
                            return $u->select('id','name');
                        }
                    ])->select('id','title','price', 'slug', 'created_by');
                }
            ])->where(['unique_ticket' => $request['query']['unique_ticket'], 'created_by' => Auth::user()->id])->first();
        if(!empty($data['support_ticket'])){
            return response()->json($data);
        }else{
            return response()->json(['status' => false, 'message' => 'You can not access another user ticket thread' ]);  
        }
    }

    /******* Get Order List *******/

    public function getOrderList(){
        $orders = $this->order->select('product_id')
            ->with([
                'product' => function($productQy){
                    return $productQy->select('id', 'title');
                }    
            ])->where(['created_by' => Auth::id(),'deleted_at' => null])
            ->groupBy('product_id')->get();

        return response()->json(['orders' => $orders]);
    }
}
