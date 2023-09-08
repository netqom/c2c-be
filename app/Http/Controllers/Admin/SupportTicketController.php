<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{SupportTicket,SupportTicketFile};
use App\Models\SupportTicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Auth,File,Storage;
use App\Mail\SendMail;
use Mail;

class SupportTicketController extends Controller
{
    public $supportTicket;
    public $supportTicketMessage;
    public $supportTicketFile;

    public function __construct(SupportTicket $supportTicket, SupportTicketMessage $supportTicketMessage, SupportTicketFile $supportTicketFile)
    {
        $this->supportTicket = $supportTicket;
        $this->supportTicketMessage = $supportTicketMessage;
        $this->supportTicketFile = $supportTicketFile;
    }

    public function index(Request $request)
    {
        return view('admin.help_section.list');
    }

    /** Action On Ticket **/
    public function ticketAction(Request $request){
        $user_detail = $this->supportTicket->with(['users' => function($q){
            return $q->select('id','name','email');
        }])->where('id', $request->id)->first();

        $statusUp = $this->supportTicket->where('id',$request->id)->update(['status' => $request->status]);
        if ($statusUp) {
            $title = "Your Ticket has been ".$request->status;
            $url = env('FRONTEND_APP_URL')."/auth/help-center?search_string=".$user_detail->unique_ticket;
            $body = "<p>Hi ".$user_detail->users->name."</h4><p>".$title." Please click on the link to check the status of ticket. <br><br> <a style='color: #0d6efd; font-size: 15px; text-decoration: none;' href=".$url.">".$url."</a></p><br><br>
            Thank you.";
            $subject = "Your Ticket has been ".$request->status. " by Admin";
       
            Mail::send('emails.ticketStatusUpdated', ['content' => $body], function($message) use($request,$subject,$user_detail){
            $message->to($user_detail->users->email);
            $message->subject($subject);
            });
            return response()->json(['type' => 'success', 'msg' => 'Ticket status updated successfully.']);
        } else {
            return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError')]);
        }
    }

    /** List Of Tickets   ***/
    public function getData(Request $request)
    {
        $queryObj = $this->supportTicket->listOfTickets($request);
        $data = $this->prepareData($queryObj, $request);
        return response()->json($data, 200);
    }

    /** Delete Product And Their Images **/
    public function deleteTicket(Request $request)
    {
        $result = $this->supportTicket->deleteItem($request->item_id);
        if ($result) {
            return response()->json(['status' => true, 'message' => 'Ticket removed successfully']);
        } else {
            return response()->json(['status' => false, 'message' => Lang::get('auth.someError')]);
        }
    }

    /******* Get Chat For Ticket *********/
    public function getChatForTicket(Request $request)
    {
        $uniqueTicket = request()->segment(count(request()->segments())); 
        $ticketData = $this->supportTicket->with(['product' => function($q){
            return $q->with('users')->select('id','title','price','slug', 'created_by')->withTrashed();
        }])->where('unique_ticket',$uniqueTicket)->first();
        
        if(empty($ticketData)){
            return redirect('admin/help-center')->with(['custom_error' => 'Ticket does not exist anymore']);
        }
        $ticketCreator = $ticketData->created_by;
        return view('admin.help_section.view',compact('ticketCreator','ticketData'));
    }

    /*** Send Message Function ****/
    public function sendMessage(Request $request)
    {
        $saveMessage = $this->supportTicketMessage->sendMessage($request);
    
        if ($request->hasFile('file')) {
            $this->supportTicketFile->saveMessageFile($request, $saveMessage);
        }
        if ($saveMessage != '') {
            return response()->json(['status' => true, 'message' => '']);
        }
        return response()->json(['status' => false, 'message' => 'Something went wrong']);
    }

    /******* Get Chat For Ticket *********/
    public function getTicketChat(Request $request)
    {
        $queryObj = $this->supportTicketMessage->getChatForTicket($request);
        $data = $this->prepareData($queryObj, $request);
        return response()->json($data);
    }

}
