<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User,Product,Order,Notification};
use Illuminate\Http\Request;
use Validator, Auth;

class NotificationsController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
	protected $notification;
	protected $user;
	protected $product;
	protected $order;
	
    public function __construct(Notification $notification, User $user, Product $product, Order $order)
    {
		$this->notification = $notification;
		$this->user         = $user;
		$this->product      = $product;
		$this->order        = $order;
    }

    public function index(Request $request)
    {   
		return view('admin.notification.list');
    }
	
	public function getData(Request $request)
	{  
		$queryObj = $this->notification->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request);
		return response()->json($data,200);
	}
	
    /* Admin notification list */
    public function notificationList(Request $request)
	{         
		$user = User::where(['role' => 1])->first();
		$html = ''; $resType = 'failure'; 
		if(!empty($user)){
			$notifications = $this->notification->where(['user_id' => $user->id, 'read_status' => 0])->orderBy('id','desc')->get();
			$notificationType = config('const.notificationType');
			$html = view('admin.notification.header_notification', compact('notifications', 'notificationType'))->render();
			$resType = 'success';
		}
		return response()->json(['type' => $resType, 'data' => $html]);
	}

	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $result = $this->notification->deleteItem($id);
		if($result){
			return response()->json(['type' => 'success', 'msg' => 'Notification deleted successfully']);
		}else{
			return response()->json(['type' => 'success', 'msg' => 'Some error occured please try again']);
		}
    }
	
}
