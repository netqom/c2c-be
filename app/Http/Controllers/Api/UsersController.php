<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ConnectAccount,Country,User,Product,Order,Category,Notification,ReviewRating,OrderPayout,Page,UkState,UkCity};
use Illuminate\Support\Facades\{Lang,Validator,Hash};
use Auth;
use Carbon\Carbon;
use App\Mail\SendMail;
use Mail;
use Stripe;

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
    public $orderPayout;
    public $category;
    public $notifications;
    public $reviewRating;
    public $pageModel;
    public $ukState;
    public $ukCity;
    public $connectAccount;

    public function __construct(ConnectAccount $connectAccount,Page $pageModel,OrderPayout $orderPayout, ReviewRating $reviewRating, Notification $notifications,User $user, Country $country, Product $product, Order $order, Category $category,UkState $ukState,UkCity $ukCity){
        $this->user    = $user;
        $this->country = $country;
        $this->product = $product;
        $this->order = $order;		
        $this->orderPayout = $orderPayout;      
        $this->category = $category;      
        $this->notifications = $notifications;      
        $this->reviewRating = $reviewRating;      
        $this->pageModel = $pageModel;
        $this->ukState = $ukState;
        $this->ukCity = $ukCity;      
        $this->connectAccount = $connectAccount; 
        $stripe = new \Stripe\StripeClient('sk_test_51AQiehF6b776uFpzWVSYni0fxHUoo079bjFflk8ZVXaiCtY1jLNz9QHDJofn3R4ThhR2L7ZbWu0rWAARximmt2pe00qnDlsjdY');     
        //pk_test_sAc5tEwjiSY1N9wXxtGELENx
        $this->stripe = $stripe;
    }

    

    /** Add Payment Account For User **/
    public function addPaymentAccount(Request $request)
    {
        $validator = Validator::make($request->all(),['paypal_email' => ['required','email']]);
        if($validator->fails()){
            return response()->json(['status' => false,  'message' => $validator->errors()->first()]);
        }
       
        $user = User::find(Auth::id());
        $updateData = []; $message='Success';
        if($user->paypal_email!= $request->paypal_email || $request->act=='resendCode') {
         $code = generateRandomString(6);    
         $body = "<strong>".$code."</strong><br><br>
						Thank you.";
         $subject = "Verify Paypal Email.";
         Mail::send('emails.paypalEmailVerificationCode', ['content' => $body], function($message) use($request,$subject){
            $message->to($request->paypal_email);
            $message->subject($subject);
         });
         $updateData['paypal_email_verification_code'] = $code;
         $updateData['is_paypal_email_verified'] = 0;
         $message = 'We have sent you a code please check your email and verify.';
        }
        $updateData['paypal_email'] = $request->paypal_email;
        
        $checkUserUpdate = $this->user->where('id',Auth::id())->update($updateData);
        
        if($checkUserUpdate){
            $userData = User::find(Auth::id());
            return response()->json(['status' => true, 'message' => $message, 'data'=>$userData ]);
        }
        return response()->json(['status' => false, 'message' => Lang::get('auth.someError') ]);
    }

    public function verifyPaymentEmail(Request $request){
        $validator = Validator::make($request->all(),['verificationCode' => ['required']]);
        if($validator->fails()){
            return response()->json(['status' => false,  'message' => $validator->errors()->first()]);
        }
        $user=User::where(['id'=>Auth::id(),'paypal_email_verification_code'=>$request->verificationCode])->first();
        if($user){
            $updateData['paypal_email_verification_code'] = NULL;
            $updateData['is_paypal_email_verified'] = 1;
            $checkUserUpdate = $this->user->where('id',Auth::id())->update($updateData);
           
            $status = true; $message = 'Success';
        }else{ $status = false; $message = ' Failed'; }
         $userData = User::find(Auth::id());
        return response()->json(['status' => $status, 'message' => $message, 'data'=>$userData ]);
    
    }

    
    /** Get Homw Page Data **/
    public function homePage(Request $request)
    {
        $data['most_view_products']     = $this->product->mySelect()->where(['status' => 1])->orderBy('views','asc')->limit(8)->get();
        $data['new_products']           = $this->product->mySelect()->where(['status' => 1])->latest()->limit(8)->get();
        $data['recently_sold_product']  = $this->product->mySelect()->has('orders')->where(['status' => 1])->orderBy('created_at','asc')->limit(8)->get();
        $data['categories']             = $this->category->select('id as value','name as label')->where('status', '1')->get();
        
        $data['home_page_content']      = $this->pageModel->with(['pageContents' => function ($pageContentsQuery) {
                                                                return $pageContentsQuery->select('page_id','param','value');
                                                            }])->where('slug','home-page')->first(); 

        if(!empty($data['most_view_products'])){
            return response()->json(['status' => true, 'message' => '', 'data' => $data ]);
        }
        return response()->json(['status' => false, 'message' => Lang::get('auth.someError')]);
    }

    /** Dashboard Function For User **/
    public function dashboard(Request $request)
    {
        $data['categories']                = $this->category->select('id as value','name as label')->where('status', '1')->get();
    	$data['numberOfProduct']           = $this->product->getNumberOfProductInApp($request)->where('created_by',Auth::id())->count();
    	$data['numberOfOrder']             = $this->order->getNumberOfOrderInApp($request,Auth::id())->count();
    	$data['totalRevenueGenerated']     = $this->order->getUserTotalRevenue($request,Auth::id())->get()->sum('order_payouts.amount');
        $data['lastMonthRevenueGenerated'] = $this->order->getUserRevenueLastMonth($request)->get()->sum('order_payouts.amount');
		$data['raiseNotificationBell']     = $this->notifications->where(['user_id' => Auth::id(), 'read_status' => 0])->count();
        
		return response()->json(['status' => true, 'message' => '', 'data' => $data]);	
    }

    /** Prepare Actual Data From Raw Data **/
    public function prepareActualDataFromRawData($rawData,$yearArray,$dateArray,$valueArray,$data)
    {
        foreach ($yearArray as $yearArrayKey => $yearArrayValue) {
            for ($i=1; $i <=  12; $i++ ){
                $label = $i > 9 ? $i : ('0'.$i);
                array_push($data['chartLabel'], $yearArrayValue .'-'. $label);
                $value = 0;
                $arrayIndex = array_search($yearArrayValue .'-'. $label, $dateArray);
                if($arrayIndex != ''){
                    $value = $valueArray[$arrayIndex];
                }
                array_push($data['chartData'], $value);
            }
        }
        $data['yearArray'] = $yearArray;
        $data['dateArray'] = $dateArray; 
        $data['valueArray'] = $valueArray;
        $data['rawData'] = $rawData;
        return $data;
    }

    /** Prepare Actual Data For Last Month **/
    public function prepareActualDataForLastMonth($rawData,$yearArray,$dateArray,$valueArray,$data)
    {
        $numberOfLabel = date('t', strtotime('last day of previous month'));
        $prevMonth = date('m', strtotime("last month"));
        for ($i=1; $i <= $numberOfLabel; $i++ ){
            $label = $i > 9 ? $i : ('0'.$i);
            array_push($data['chartLabel'], $yearArray[0] .'-'. $prevMonth .'-'. $label );
            $value = 0;
            $arrayIndex = array_search($yearArray[0] .'-'. $prevMonth .'-'. $label, $dateArray);
            if($arrayIndex != ''){
                $value = $valueArray[$arrayIndex];
            }
            array_push($data['chartData'], $value);
        }
        $data['rawData'] = $rawData;
        return $data;
    }
    
    /** Get Chart Data **/
    public function getChartData(Request $request)
    {
        $rawData = ''; $data['chartLabel'] = []; $data['chartData'] = [];

        if($request->chartFor == 'product') {
            $rawData = $this->product->getNumberOfProductForChart($request);
        }elseif ($request->chartFor == 'order_received') {
            $rawData = $this->order->getNumberOfOrderReceivedForChart($request);
        }elseif ($request->chartFor == 'user_total_revenue') {
            $rawData = $this->order->getTotalRevenueGeneratedForChart($request);
        }elseif ($request->chartFor == 'user_last_month_revenue') {
            $rawData = $this->order->getUserRevenueLastMonthForChart($request);   
        }

        if(!empty($rawData)){
            $yearArray = array_unique(array_column(json_decode(json_encode($rawData), true), 'year'));
            $dateArray = array_unique(array_column(json_decode(json_encode($rawData), true), 'date'));
            $valueArray = array_column(json_decode(json_encode($rawData), true), 'value');
            if($request->chartFor == 'user_last_month_revenue'){
                $data = $this->prepareActualDataForLastMonth($rawData,$yearArray,$dateArray,$valueArray,$data);
            }else{
                $data = $this->prepareActualDataFromRawData($rawData,$yearArray,$dateArray,$valueArray,$data);   
            }  
        }               
        return response()->json(['status' => true, 'message' => '', 'data' => $data]);  
    }

    /** Get Seller Profile **/
    public function getSellerProfile(Request $request)
    {
        $queryObj  = $this->product->getDataSearchRecords($request);
        $data['product_list'] = $this->prepareData($queryObj, $request);
        $reviewQueryObj=$this->reviewRating->getReviews($request);  
        $data['review_list'] = $this->prepareData($reviewQueryObj, $request);  
        foreach($data['review_list']['data'] as $q){ 
            if ($q instanceof \Illuminate\Database\Eloquent\Model) {
                collect($data['review_list']['data'])->map(function($q) {
                    $user=$q->user()->first();
                    $q['created_by_user_name']= $user->name;
                    $q['creater_image']= url('storage/'.$user->image_path);
                    return $q;
                });
            }
        }
        
        $data['sold_product'] = $this->order->whereHas('order_payouts', function($orderPayoutQuery){
			$orderPayoutQuery->where('transaction_status', 'SUCCESS');
		})->where(['payment_status' => 'COMPLETED' , 'product_owner' => $request['query']['seller_id']])->count();
        $data['seller_detail'] = $this->user->find($request['query']['seller_id'],['id','name','image_path','created_at','avg_rating']);
        return response()->json(['status' => true, 'message' => '', 'data' => $data ]);
    }

    /** Get Country List ***/
    public function countryList(Request $request)
    {   
        $countryListData = $this->country->select('id as value','name as label')->orderBy('name', 'asc')->get();
        if(!empty($countryListData)){
            return response()->json(['status' => true, 'data' => $countryListData]);
        }
        return response()->json(['status' => false, 'data' => []]);
    }
    public function stateList(Request $request)
    {   
        $stateListData = $this->ukState->select('id as value','name as label')->orderBy('name', 'asc')->get();
        if(!empty($stateListData)){
            return response()->json(['status' => true, 'data' => $stateListData]);
        }
        return response()->json(['status' => false, 'data' => []]);
    }
    public function cityList(Request $request)
    {   
        $cityListData = $this->ukCity->select('id as value','name as label')->where('state_id',$request->state_id)->orderBy('name', 'asc')->get();
        if(!empty($cityListData)){
            return response()->json(['status' => true, 'data' => $cityListData]);
        }
        return response()->json(['status' => false, 'data' => []]);
    }

    /** Add Edit User Profile Data ***/
    public function getUserFormData(Request $request)
    {
        $title = '';
        $cities = [];
        if($request->id == 0){
            $data = new User();
            $title = 'Add User';
        }else{
            $data = $this->user->find($request->id);
            $title = 'Edit User';
            if($data->state_id){
                $cities = $this->ukCity->select('id as value','name as label')->where('state_id',$data->state_id)->orderBy('name', 'asc')->get(); 
            }
        }
        $states = $this->ukState->select('id as value','name as label')->orderBy('name', 'asc')->get();
        
        return response()->json(['status' => false, 'message' => '','data' => $data, 'states' => $states, 'title' => $title,'cities'=>$cities ]);
    }

    /* Change Password Method*/
    public function updatePassword(Request $request)
    {
        $request->validate(['old_password' => 'required', 'new_password' => 'required|confirmed' ]);

        #Match The Old Password
        if(!Hash::check($request->old_password, auth()->user()->password)){
            return response()->json(['status' => false, 'message' => Lang::get('application.user.oldPasDoeNotMatch')]);
        }

        #Update the new Password
        $userUpdatePass = User::whereId(auth()->user()->id)->update([ 'password' => Hash::make($request->new_password) ]);
        if($userUpdatePass){
            return response()->json(['status' => true, 'message' => Lang::get('application.user.pasChanSuccess')]);
        }
        return response()->json(['status' => false, 'message' => Lang::get('auth.someError') ]);
    }

    public function addupdateUser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required','string','max:250'],
            'email' => $request->item_id == 0 ? ['required','unique:users,email'] : ['required','unique:users,email,'.$request->item_id.',id'],
            'phone' => ['required'],
            'role' => 'required',
            'address' => 'required',
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => false,  'message' => $validator->errors()->first()]);
        }
        $result = $this->user->createUpdateItem($request);
        if($result){
            return response()->json(['status' => true, 'message' => 'User updated successfully','data' => $result]);
        }
        return response()->json(['status' => false, 'message' => Lang::get('auth.someError') ]);
    }
	
}
