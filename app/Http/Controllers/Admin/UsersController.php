<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\UkState;
use App\Models\UkCity;
use Validator, Auth;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public $country;
    public $user;
    public $product;
    public $order;
    public function __construct(User $user, Country $country, Product $product, Order $order,UkState $ukState,UkCity $ukCity){
        $this->user    = $user;
        $this->country = $country;
        $this->product = $product;
        $this->order   = $order;
		$this->ukState = $ukState;
        $this->ukCity = $ukCity;  
		
    }
    public function index(Request $request)
    {   
		return view('admin.users.list');
    }
	
	public function getData(Request $request)
	{   
		$queryObj = $this->user->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request);
		return response()->json($data,200);
	}
	
	public function getAddEditForm($id)
	{
		$title = '';
		$cities = [];
		if($id == 0){
			$data = new User();
			$title = 'Add User';
		}else{ 
			$data = $this->user->find($id);
			//check if Admin
			if($data->role == 1){
				if($data->id != Auth::id()){
					return response()->json(['type' => 'error', 'msg' => "You don't have permission for this operation."]);	
				}
				$title = 'Update Profile';	
			}else{
				$title = 'Edit User';
			}
			$cities = $this->ukCity->select('id','name')->where('state_id',$data->state_id)->orderBy('name', 'asc')->get();
		    
		}
		$positions = config('const.user_roles');
		unset($positions[0]);
		$states = $this->ukState->select('id','name')->orderBy('name', 'asc')->get();
		$html = view('admin.users.add-edit', compact('data', 'positions','states','cities'))->render();
		return response()->json(['type' => 'success', 'msg' => '', 'title' => $title, 'html' => $html, 'data' => $data]);
	}
	
	public function addupdateUser(Request $request)
	{
		$inputs = $request->all();
		if($request->item_id == 0){
			$validator = Validator::make($inputs,[
				'name' => ['required','string','max:250'],
				'email' => ['required','unique:users,email'],
				'phone' => ['required'],
				'role' => 'required',
			]);
		}else{
			$validator = Validator::make($inputs,[
				'name' => ['required','string','max:250'],
				'email' => ['required','unique:users,email,'.$request->item_id.',id'],
				'phone' => ['required'],
				'role' => 'required',
			]);
		}

        if($validator->fails()){
			return response()->json(['type' => 'error', 'msg' => $validator->errors()->first()]);
		}
        
		$result = $this->user->createUpdateItem($request);
		if($result){
			return response()->json(['type' => 'success', 'msg' => 'User saved successfully' , 'data' => $result]);
		}else{
			return response()->json(['type' => 'success', 'msg' => 'Some error occured please try again']);
		}
	}
	
	public function viewProfile(Request $request, $id,$tab=null)
	{         
		$user = $this->user->find($id); 
		$user_id = $user ? $user->id : Auth::id(); 
		$product_count = $this->product->where(['created_by' => $id, 'status' => 1])->count();
    	$order_count   = $this->order->getNumberOfOrderInApp($request,$user_id)->count();
		// $purchase_count =  $this->order->whereHas('order_payouts', function($orderPayoutQuery){
		// 	$orderPayoutQuery->where('transaction_status', 'SUCCESS');
		// })->where(['created_by' => $id , 'payment_status' => 'COMPLETED'])->count(); 
		$purchase_count =  $this->order->where(['created_by' => $id])->count(); 
    	$revenue = $this->order->getUserTotalRevenue($request,$id)->get()->sum('order_payouts.amount');
			
		return view('admin.users.profile', compact('user', 'product_count', 'order_count', 'revenue','tab','purchase_count'));
	}
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
		
        $result = $this->user->deleteItem($id);
		if($result){
			return response()->json(['type' => 'success', 'msg' => 'User deleted successfully']);
		}else{
			return response()->json(['type' => 'success', 'msg' => 'Some error occured please try again']);
		}
    }
	
	 /**
     * Change Admin User Password.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
	public function changePassword(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs,[
			'password' => 'required|string|min:6|max:15|same:confirm_password',
			'confirm_password' => 'required|same:password',
		]) ;
		if($validator->fails()){
			return response()->json(['type' => 'error', 'msg' => $validator->errors()->first()]);
		}
		$users = $this->user->changePassword($inputs);
		if($users){
			return response()->json(['type' => 'success', 'msg' => 'Password updated successfully']);
		}else{
			return response()->json(['type' => 'error', 'msg' => 'Some error occured please try again']);
		}
        
    }
	
	public function changeStatus(Request $request)
	{
		  User::where('id',$request->id)->update(['status'=>$request->changeToStatus]);
		  return response()->json(['type' => 'success', 'msg' => 'User status '.$request->changeToStatusText.' successfully']);
	}
	public function cityList(Request $request)
    {   
        $cityListData = $this->ukCity->select('id','name')->where('state_id',$request->state_id)->orderBy('name', 'asc')->get();
        if(!empty($cityListData)){
            return response()->json(['type' => 'success', 'data' => $cityListData]);
        }
        return response()->json(['type' => 'success', 'data' => []]);
    }
}
