<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\{Product,Notification,OrderPayout,ReviewRating,Setting,ConnectAccount,Message,Order};
use Auth,DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendDynamicMailJob;
class Order extends Model
{

	protected $appends = ['payment_status_name', 'payment_method_name'];

	/*** Get USER Last Month  Revenue Generated For Chart Only**/
	public function getUserRevenueLastMonthForChart($request){

		return $this->select('orders.id', DB::raw('sum(order_payouts.amount) as value'),DB::raw('YEAR(order_payouts.created_at) year, MONTH(order_payouts.created_at) month'),	DB::raw("DATE_FORMAT(order_payouts.created_at, '%Y-%m-%d') date"), 'order_payouts.order_id')
    				->join('order_payouts', 'order_payouts.order_id', '=', 'orders.id')
    				->where(['orders.product_owner' => Auth::id() , 'order_payouts.transaction_status' => 'SUCCESS'])
    				->whereMonth('order_payouts.created_at', '=', Carbon::now()->subMonth()->month)
    				->get();

	}

	/** Get Total Revenue Generated For Chart Only User **/
	public function getTotalRevenueGeneratedForChart($request)
	{
		return $this->select('orders.id', DB::raw('sum(order_payouts.amount) as value'),DB::raw('YEAR(order_payouts.created_at) year, MONTH(order_payouts.created_at) month'),	DB::raw("DATE_FORMAT(order_payouts.created_at, '%Y-%m') date"), 'order_payouts.order_id')
    				->join('order_payouts', 'order_payouts.order_id', '=', 'orders.id')
    				->where(['orders.product_owner' => Auth::id() , 'order_payouts.transaction_status' => 'SUCCESS'])
    				->groupby('year','month')
    				->get();
	}

	/** Get Number Of Order Received For Chart Only User **/
	public function getNumberOfOrderReceivedForChart($request)
	{
		return $this->select('orders.id', DB::raw('count(order_payouts.id) as value'),DB::raw('YEAR(order_payouts.created_at) year, MONTH(order_payouts.created_at) month'),	DB::raw("DATE_FORMAT(order_payouts.created_at, '%Y-%m') date"), 'order_payouts.order_id')
    				->join('order_payouts', 'order_payouts.order_id', '=', 'orders.id')
    				->where(['orders.product_owner' => Auth::id() , 'order_payouts.transaction_status' => 'SUCCESS'])
    				->groupby('year','month')
    				->get();
	}
	
	/*** Get Total Revenue Generated For Whole App **/
	public function getTotalRevenue($user_id)
	{
		return $this->where(['payment_status' => 'COMPLETED']);
	}


	/**  Get Total Revenue Generated For Only User Only  */
	public function getUserTotalRevenue($request,$userId)
	{
		return $this->whereHas('order_payouts', function($orderPayoutQuery){
			$orderPayoutQuery->where('transfer_id', '!=', '');
		})->where(['product_owner' => $userId , 'payment_status' => 'COMPLETED'])
		->where('status', 'succeeded')
		->orWhere('status', 'processing');
			
	}

	/*** Get User Last Month  Revenue Generated Only **/
	public function getUserRevenueLastMonth($request){

		return $this->whereHas('order_payouts', function($orderPayoutQuery){
			$orderPayoutQuery->where('transaction_status', 'SUCCESS')->whereMonth('created_at', '=', Carbon::now()->subMonth()->month);
		})->where(['payment_status' => 'COMPLETED', 'product_owner' => Auth::id()]);

	}

	/*** Get Number Of Revenue Generated In App **/
	public function getNumberOfRevenueGeneratedInLastMonth($request){

		return $this->where('payment_status',2)->whereMonth('created_at', '=', Carbon::now()->subMonth()->month);

	}

	/*** Get Number Of Order Into The App For User And Admin Also **/
	public function getNumberOfOrderInApp($request,$userId)
	{
		return $this->whereHas('order_payouts', function($orderPayoutQuery){
			// $orderPayoutQuery->where('transaction_status', 'SUCCESS');
		})->where('product_owner',$userId)
		->where(function($q){
			$q->where('payment_status','COMPLETED')
			->orWhere('payment_status','REFUNDED');
		});

	}
	
	/** Get the virtual payment status name **/
	public function getPaymentStatusNameAttribute() 
	{
		return $this->payment_status;
	}
	
	public function getPaymentMethodNameAttribute() 
	{
		return $this->payment_method;
	}
	
	public function user(){
		return $this->belongsTo(User::class, 'created_by');
	}

	public function review_rating(){
		return $this->hasOne(ReviewRating::class);
	}

	public function connect_account(){
		return $this->hasOne(ConnectAccount::class,'user_id','product_owner');
	}
	
	public function product(){
		return $this->belongsTo(Product::class, 'product_id')->withTrashed();
	}

	public function order_payouts(){
		return $this->hasOne(OrderPayout::class);
	}
	
    public function getMonthWiseData($date)
	{
		$date_array = explode('-', $date);
		return  $this->whereYear('created_at', $date_array[1])
                         ->whereMonth('created_at', $date_array[0])
                        ->sum('amount');		
	}

	/*** Get the product images for the orders using product_id. ***/
    public function product_images(){
        return $this->hasMany(ProductImage::class,'product_id', 'id');
    }

    /** Generate Random String **/
    public function generateRandomString($length = 10) {
	    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
	}

    /** Create And Update Particular Order By Stripe **/
    public function createAndUpdateByStripe($request){
		$buyer_id = Auth::id();
    	$settings = Setting::where('key','admin_commission_value')->first();
        $adminCommisionPercentage = $settings['value'];
        $adminCommision = ( $request->price ) * ( ($adminCommisionPercentage) / (100) );
		$uniOrderId = $this->generateRandomString();
    	if($request->item_id == 0){
			$order = new Order();
			$order->created_by			= Auth::id();
			$order->uuid				= $uniOrderId;
			$order->payment_intent_id  	= $request->payment_intent_id;
			$order->client_secret  		= $request->client_secret;
			$order->product_owner  		= $request->product_owner;
			$order->payee_email  		= $request->payee_email;
			$order->payment_method  	= $request->payment_method;
			$order->amount  			= $request->amount;
			$order->price  				= $request->price;
			$order->delivery_price 		= $request->delivery_price;
			$order->currency  			= $request->currency;
			$order->product_id  		= $request->product_id;
			$order->product_title  		= $request->product_title;
			$order->quantity  			= $request->quantity;
			$order->address  			= Auth::user()->address;
			$order->admin_commission_setting_value 	= $adminCommisionPercentage;
			$order->admin_commission_value 			= $adminCommision;
			$order->payment_status = $request->status == 'succeeded' ||   $request->status == 'processing' ? 'COMPLETED' : $request->status;
			$order->object 		   = $request->object;
		}else{
			$order = Order::find($request->item_id);
		}
		$order->status  		= $request->status;
		$order->updated_by  	= Auth::id();
		if($order->save()){
			if($request->item_id == 0){
				$productDetail = Product::find($request->product_id);
				$productDetail->quantity = $productDetail->quantity-1; 
				$productDetail->save();
				$sellerDetail = User::find($request->product_owner);
				$admin = User::where('role',1)->first();
				$notificationReceiverIds = [$request->product_owner,$admin->id,$buyer_id]; 
				
				foreach ($notificationReceiverIds as $key => $receiver) {
					$notification = new Notification();
					$notification->user_id     = $receiver;
					$notification->type        = 1;
					$notification->item_id     = $order->id;
					if($admin->id == $receiver){
						$notification->description = "A product (".$productDetail->title.") of ".$sellerDetail->name." has been purchased by ".Auth::user()->name;
					}else{  
						if($request->product_owner != $buyer_id){
							$notification->description = "Your product (".$productDetail->title.") has been purchased by ".Auth::user()->name.". To check the order invoice <a href='".env("FRONTEND_APP_URL")."/auth/order-invoice/".$uniOrderId."'>Click here</a>";
						}
						$notification->description = "Your order (".$productDetail->title.") has been placed successfully. To check the order invoice <a href='".env("FRONTEND_APP_URL")."/auth/order-invoice/".$uniOrderId."'>Click here</a>";
					}					
					$notification->status      = 1;
					$notification->created_by  = Auth::id();
					$notification->updated_by  = Auth::id();
					$notification->save();
				}

				//sent mail to admin, buyer and seller
				$this->sendOrderPurchasedMail($notificationReceiverIds,$admin,$productDetail, $sellerDetail, $order, $uniOrderId);	
				if(!empty($request->chat_offer)){
					Message::where('id',$request->chat_offer['id'])->update(['is_purchased'=> 1]);
					Message::where(['product_id' => $request->product_id, 'has_offer' => 1 ])->update(['has_offer' => 0, 'offer_response'=> 2]);
				}
				
			}
			return $order;
		}
    	return false;
    }

    /** Create And Update Particular Order **/
    public function createAndUpdate($request){
    	$settings = Setting::where('key','admin_commission_value')->first();
    	$adminCommision = $settings['value'];
        $amountThatNeedToPayPayee = ($request->price) * ( ($adminCommision) / (100) );
    	if($request->item_id == 0){
			$order = new Order();
			$order->created_by		= Auth::id();
			$order->uuid			= $request->uuid;
			$order->intent  		= $request->intent;
			$order->payer_email  	= $request->payer_email;
			$order->merchant_id  	= $request->merchant_id;
			$order->capture_id  	= $request->capture_id;
			$order->payer_id  		= $request->payer_id;
			$order->product_owner  	= $request->product_owner;
			$order->payee_email  	= $request->payee_email;
			$order->payment_method  = $request->payment_method;
			$order->amount  		= $request->amount;
			$order->price  			= $request->price;
			$order->delivery_price 	= $request->delivery_price;
			$order->currency  		= $request->currency;
			$order->product_id  	= $request->product_id;
			$order->quantity  		= $request->quantity;
			$order->payment_token  	= $request->payment_token;
			$order->admin_commission_setting_value 	= $request->admin_commission_setting_value;
			$order->admin_commission_value 			= $request->admin_commission_value;
		}else{
			$order = Order::find($request->item_id);
		}
		$order->payment_status  = $request->payment_status;
		$order->status  		= $request->status;
		$order->updated_by  	= Auth::id();
		if($order->save()){
			if($request->item_id == 0){
				$productDetail = Product::find($request->product_id);
				$productDetail->quantity = $productDetail->quantity-1; 
				$productDetail->save();
				$sellerDetail = User::find($request->product_owner);
				$admin = User::where('role',1)->first();
				$notificationReceiverIds = [$request->product_owner,$admin->id]; 
				foreach ($notificationReceiverIds as $key => $receiver) {
					$notification = new Notification();
					$notification->user_id     = $receiver;
					$notification->type        = 1;
					$notification->item_id     = $order->id;
					if($admin->id == $receiver){
						$notification->description = "A product (".$productDetail->title.") of ".$sellerDetail->name." has been purchased by ".Auth::user()->name;
					}else{  
						$notification->description = "Your product (".$productDetail->title.") has been purchased by ".Auth::user()->name.". To check the order invoice <a href='".env("FRONTEND_APP_URL")."/auth/order-invoice/".$request->uuid."'>Click here</a>";
					}					
					$notification->status      = 1;
					$notification->created_by  = Auth::id();
					$notification->updated_by  = Auth::id();
					$notification->save();
				}
				
			}
			return $order;
		}
    	return false;
    }

    /** Get sold Product Detail **/
    public function soldProductDetail($request){
    	return  $this->with([
    				'product' => function ($productQuery) {
		    			return $productQuery->select('id','title','price','description');
		    		},
		    		'user' => function ($userQuery) {
		    			// return $userQuery->with([
		    			// 	'country' => function ($countryQuery) {
		    			// 		return $countryQuery->select('id','name');
		    			// 	}
		    			// ])->select('id','name','image_path','country_id','address','email','phone');
		    			return $userQuery->select('id','name','image_path','address','email','phone');
		    		}
		    	])
				->select('id','uuid','created_at','updated_at','created_by','product_id','delivery_price','payer_email','payee_email','capture_id','amount','discount','price','currency')->where('uuid',$request->uuid);
    }
	
	/** Get the list Of My Orders, Sold Orders , All Orders **/
	public function getDataTotalRecords($request)
    {  
		$search       = isset($request['query']['search_string']) ? $request['query']['search_string'] : '';
		$order_colomn = isset($request['sort']['field']) ? $request['sort']['field'] : 'id';
		$sort_type   = isset($request['sort']['sort']) ? $request['sort']['sort'] : 'desc';
		$status = isset($request['query']['status']) ? $request['query']['status'] : '';
		$startDate    = isset($request['start_date']) ? $request['start_date'] : '';
		$endDate      = isset($request['end_date']) ? $request['end_date'] : '';

		$query = $this->select('orders.id','orders.created_by','orders.product_id','orders.payment_status','orders.uuid', 'orders.amount', 'orders.discount', 'orders.tax','orders.status','orders.created_at','users.name as user_name', 'orders.payment_intent_id','orders.admin_commission_value')
			->with([
				'product' => function($q){
					return $q->select('title','id','description','slug','created_by','deleted_at')->withTrashed();
				},
				'review_rating' => function($q){
					return $q->select('rating','review','order_id');
				},
				'order_payouts' => function($q){
					return $q->select('id','order_id','transaction_status','transfer_id');
				},
			]);


		$created_by = Auth::id();
	    
		if($request->filled('created_by')){
			$created_by = decryptDataId($request->created_by);
		}

		if($request->order_types != 'All Orders'){
			if($request->order_types == 'Sold Products'){  	
				$query->where('product_owner', $created_by)
						->where(function($q){
							$q->where('payment_status','COMPLETED')
							  ->orWhere('payment_status','REFUNDED');
						}); // Getting Sold Orders

				$query->when( $created_by != '' , function ($q) use($created_by) {
					$q->whereHas('order_payouts', function($orderPayoutQuery){
						// $orderPayoutQuery->where('transaction_status', 'SUCCESS');
					})->where('product_owner', $created_by);
				});
			}else{
				$query->where('created_by', $created_by);  // getting My order and empty the created variable for skip below query
				$created_by = '';
			}
		}

		$query->leftJoin('users', function($join) use($request) {
		    if($request->order_types != 'My Orders'){
	         	$join->on('orders.created_by', '=', 'users.id');
		   	}else{ 
				$join->on('orders.product_owner', '=', 'users.id');
		   	} 
	    });	

		$query->when($search != '', function ($q) use ($search) {
			$q->where(function ($query)use ($search) {
				$query->where('users.name',  'like',  '%' . $search . '%')
					->orWhere('orders.uuid',  'like',  '%' . $search . '%')
					->orWhere('orders.amount',  'like',  '%' . $search . '%')
					->orWhere('orders.discount',  'like',  '%' . $search . '%')
					->orWhere('orders.tax',  'like',  '%' . $search . '%');
			});
		});
		
		$query->when(($startDate != '') && ($endDate != ''),function($query) use($startDate,$endDate){
			// echo $startDate.'<=>'.date('Y-m-d', strtotime("+1 day", strtotime($endDate)));die;
			$query->whereBetween('orders.created_at', [$startDate, date('Y-m-d', strtotime("+1 day", strtotime($endDate)))]);
		});

		$query->when($status != '', function($q) use($status){
			$q->where('orders.payment_status',  $status);
		});
		// echo $query->get();die;
		return $query->orderBy($order_colomn, $sort_type);
    }
	 
	/** Delete Order **/
	public function deleteItem($id)
	{
		$order = $this->find($id);
		$order->delete();
		return true;
	}
	public function getAdminCommission(){
	  return $this->where('status','COMPLETED')->sum('admin_commission_value');	
	}

	//Updated Payment Refund Data 
	public function updatePaymentRefundData($paymentRefund){
		$order =  $this->where('payment_intent_id', $paymentRefund->payment_intent)->first();

		$order->refund_id = $paymentRefund->id;
		$order->charge_id = $paymentRefund->charge;
		$order->refund_amount = $paymentRefund->amount / 100;
		$order->payment_status = 'REFUNDED';
		$order->object  = $paymentRefund->object;
		if($order->save()){
			$productDetail = Product::find($order->product_id);
			$productDetail->quantity = $productDetail->quantity == 0 ? 1 : $productDetail->quantity + 1 ; 
			$productDetail->save();
		}

		return true;
	
	}

	//send mail to seller, buyer and admin related order purchased.

	public function sendOrderPurchasedMail($notificationReceiverIds,$admin,$productDetail, $sellerDetail, $order, $uniOrderId){

		foreach ($notificationReceiverIds as $key => $receiver) {
			$receiverAddress = ''; 
			if($admin->id == $receiver){
				//email sent to admin
				$subject = "Product Sold of ".$sellerDetail->name;
				$content  = [
					"message" => "<h4>Hi ".$admin->name."</h4><p>A product (".$productDetail->title.") has been sold of ".$sellerDetail->name." to ".Auth::user()->name.".</p>",
					"action_url" => env("APP_URL")."/admin/orders/detail/".$order->id,
					"action_name" => "Click Me",
				];
				$receiverAddress = $admin->email;
			}else{  
				if($order->product_owner == $receiver){
					//email sent to seller
					$subject = "Product ".$productDetail->title." Successfully Sold to ".Auth::user()->name;
					$content  = [
						"message" => "<h4>Hi ".$sellerDetail->name."</h4><p>A product (".$productDetail->title.") has been successfully sold to ".Auth::user()->name."</p>",
						"action_url" => env("FRONTEND_APP_URL")."/auth/order-invoice/".$uniOrderId,
						"action_name" => "Click Me",
					];
					$receiverAddress = $sellerDetail->email;
				}else{
					//email sent to buyer
					$subject = "Product Successfully Purchased - ".$productDetail->title;
					$content  = [
						"message" => "<h4>Hi ".Auth::user()->name."</h4><p>A product (".$productDetail->title.") has been successfully purchased from ".$sellerDetail->name."</p>",
						"action_url" => env("FRONTEND_APP_URL")."/auth/order-invoice/".$uniOrderId,
						"action_name" => "Click Me",
					];

					$receiverAddress = Auth::user()->email;
				}
			}		
			dispatch(new SendDynamicMailJob($subject, $content, "emails.orderPurchasedEmail", $receiverAddress));
		}			
		
	}
}
