<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\{Notification,Product};
use Illuminate\Http\Request;
use Auth, Str, Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\{Lang,Validator}; 

class NotificationsController extends Controller
{
	
	public $notification;
	public $product;

    public function __construct(Product $product, Notification $notification){
        $this->notification = $notification;
        $this->product		= $product;
    }
	
	/** Get Sub Notification List**/
	public function getNotificationList(Request $request){
		$queryObj  = $this->notification->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request);
		//$data = ['query' => $queryObj->toSql(), 'user_id' => Auth::id()];
		return response()->json($data);
	}
	
	/** Delete Notification **/
	public function deleteNotification(Request $request)
    {
		$result = $this->notification->deleteItem($request->item_id);
        if ($result) {
			return response()->json(['status' => true, 'message' => Lang::get('application.notification.notificationRemove')]);
        } else {
			return response()->json(['status' => false, 'message' => Lang::get('auth.someError') ]);
        }
    }
	/** count unread notifications **/
	public function countUnreadNotification(Request $request){
		$count     = $this->notification->where(['user_id' => Auth::id(), 'read_status' => 0])->count();
		$draftProduct = $this->product->select('id')->where(['created_by' => Auth::user()->id, 'status' => 0])->latest()->first();
        return response()->json(['status' => true, 'message' => '', 'data' => $count , 'draft_product' => $draftProduct]);
	}

}	