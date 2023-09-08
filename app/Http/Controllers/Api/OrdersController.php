<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Country,User,Product,Order,ReviewRating,Setting,OrderPayout,Message};
use Illuminate\Support\Facades\{Lang,Validator,Auth};
use App\Classes\PayPalClient;
use PaypalPayoutsSDK\Payouts\PayoutsItemGetRequest;
use PaypalPayoutsSDK\Payouts\PayoutsPostRequest;
use PaypalPayoutsSDK\Payouts\PayoutsGetRequest;
use Illuminate\Support\Facades\Log;
use DB;

class OrdersController extends Controller
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
    public $settingModal;
    public $productReview;
    public $orderPayout;
    public $messageModel;

    public function __construct(Message $messageModel,OrderPayout $orderPayout,Setting $settingModal,User $user, Country $country, Product $product, Order $order, ReviewRating $productReview){
        $this->user          = $user;
        $this->product       = $product;
        $this->order         = $order;
        $this->productReview = $productReview;
        $this->settingModal  = $settingModal;
        $this->orderPayout   = $orderPayout;
        $this->messageModel  = $messageModel;
    }

    /** List Of All Orders , Sold Product, My Order  Along With Pagination**/
    public function getListAccordingRoute(Request $request)
	{
		$ordersType = config('const.order_types');
		$request->merge(['order_types' => $ordersType[1]]);
		if($request->route()->getName() == 'sold.products'){
			$request->merge(['order_types' => $ordersType[3]]);
		}elseif ($request->route()->getName() == 'my.orders') {
			$request->merge(['order_types' => $ordersType[2]]);
		}		
		
		$queryObj  = $this->order->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request);
		
		return response()->json($data);
	}

	/** Checkout Method Start From Here **/
	public function getCheckoutDetail(Request $request)
	{
		$data = $this->product->productDetail($request)->first();
		$commisionForAllUsers = $this->settingModal->where('key','admin_commission_value')->first();
		if(empty($data)){			
			return response()->json(['status' => false, 'message' => Lang::get('application.product.doesNotExsitProduct')]);
		}
		$data['admin_commision'] = 0;
		if(!empty($commisionForAllUsers)){
			$data['admin_commision'] = $commisionForAllUsers['value'];
		}
		$data['chat_offer'] = [];
		if($request->chat_id != ''){
			$data['chat_offer'] = $this->messageModel->where(['chat_id' => $request->chat_id, 'offer_response' => 1, 'is_purchased' => 0])->latest()->first();
		}
		return response()->json(['status' =>true, 'data' => $data, 'message' => '']);
	}


	/** Create Order From Here **/
	public function saveOrder(Request $request)
	{
		if($request->type == 'paypal'){
			$validator = Validator::make($request->all(), [
				'amount' => 'required',
				'price' => 'required',
				'product_id' => 'required',
				'quantity' => 'required',
				'payment_method' => 'required',
				'payment_status' => 'required',
				'status' => 'required'
			]);
	        if ($validator->fails()) {
	            return response()->json(['status' => false, 'message' => '', 'errors' => $validator->errors()]);
	        }   
	        $order = $this->order->createAndUpdate($request);
		}else{
			$validator = Validator::make($request->all(), [
				'amount' => 'required',
				'price' => 'required',
				'product_id' => 'required',
				'quantity' => 'required',
				'payment_method' => 'required',
				'status' => 'required'
			]);
	        if ($validator->fails()) {
	            return response()->json(['status' => false, 'message' => '', 'errors' => $validator->errors()]);
	        }  
	        $order = $this->order->createAndUpdateByStripe($request); 
	        if($order){
	        	// Create Order Payout For Particular Order
	        	$this->orderPayout->createAndUpdatePayoutByStripe($order);
	        }
		}
		 
		
		if($order){
			return response()->json(['status' => true, 'message' => '', 'data' => $order]);
		}else{
			return response()->json(['status' => false, 'message' => Lang::get('auth.someError'), 'data' => [] ]);
		}
	}

	/** Get Sold Product detail **/
	public function soldProductDetail(Request $request)
	{
		//return response()->json(['status' => true, 'message' => '', 'data' => []]);
		$order = $this->order->soldProductDetail($request)->first();
		if(!empty($order)){
			return response()->json(['status' => true, 'message' => '', 'data' => $order]);
		}else{
			return response()->json(['status' => false, 'message' => Lang::get('auth.someError'), 'data' => [] ]);
		}
	}

	/** Create a payout **/
	public function savePayout(Request $request)
	{
		$orderPayout = $this->orderPayout->createAndUpdatePayout($request);
		if($orderPayout){
			return response()->json(['status' => true, 'message' => '']);
		}else{
			return response()->json(['status' => false, 'message' => Lang::get('auth.someError')]);
		}
	}
	
	public function getReviewFormData(Request $request)
	{
		$review = $this->productReview->where(['order_id' => $request->order_id, 'product_id' => $request->product_id, 'created_by' => Auth::id()])->first();
		return response()->json(['status' => true, 'message' => '', 'data' => $review]);
	}
	
	public function addUpdateReviewRating(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'review' => 'required',
			'rating' => 'required',
			'product_id' => 'required',
			'order_id' => 'required',
		]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => '', 'errors' => $validator->errors()]);
        }    
		$review = $this->productReview->createAndUpdate($request);
		if($review){
			return response()->json(['status' => true, 'message' => Lang::get('application.review.addUpdate') , 'data' => $review]);
		}else{
			return response()->json(['status' => false, 'message' => Lang::get('auth.someError'), 'data' => [] ]);
		}
	}

	public function getUnclaimedAmount(Request $request){
		$amount = DB::table('orders')
		->join('order_payouts', 'orders.id', '=', 'order_payouts.order_id')
		->join('users', 'orders.product_owner', '=', 'users.id')
		->select('orders.currency','orders.product_owner','orders.id','order_payouts.id as order_payout_id', 'order_payouts.order_id','order_payouts.amount as order_payout_amount', 'order_payouts.payout_batch_id','order_payouts.batch_status','order_payouts.payout_item_id','order_payouts.transaction_status','users.paypal_email as product_owner_email')
		->where(['order_payouts.transaction_status'=>'UNCLAIMED','orders.product_owner'=>Auth::id()])
		->sum('order_payouts.amount');
		$user=User::find(Auth::id());
		$isPaypalEmailVerified = $user->is_paypal_email_verified;
		return response()->json(['status' => true, 'message' => '', 'amount' => $amount,'isPaypalEmailVerified'=>$isPaypalEmailVerified]);
	}
	protected function generateRandomString($length) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    protected function buildPayoutData($amount,$currency,$email) {
        return [
            "sender_batch_header" =>  [
                "sender_batch_id" => self::generateRandomString(10),
                "email_subject" => "You have a payout!",
                "email_message" => "You have received a payout! Thanks for using our service!"
            ],
            "items" =>  [
                [
                    "recipient_type" => "EMAIL",
                    "amount" => [
                        "value" => number_format($amount, 2),
                        "currency" => $currency,
                    ],
                    "receiver" => $email,
                    "note" => "Thanks for your support!",
                ],
            ],
        ];
    }
	public function claimAmount(Request $request){
		
		$ordersDetail = DB::table('orders')
		->join('order_payouts', 'orders.id', '=', 'order_payouts.order_id')
		->join('users', 'orders.product_owner', '=', 'users.id')
		->select('orders.currency','orders.product_owner','orders.id','order_payouts.id as order_payout_id', 'order_payouts.order_id','order_payouts.amount as order_payout_amount', 'order_payouts.payout_batch_id','order_payouts.batch_status','order_payouts.payout_item_id','order_payouts.transaction_status','users.paypal_email as product_owner_email')
		->where(['order_payouts.transaction_status'=>'UNCLAIMED','orders.product_owner'=>Auth::id()])
		->get();
	   
		
		if(!empty($ordersDetail))
        {
            foreach ($ordersDetail as $orderKey => $orderVal)
            {
                $orderNeedsToBeCreated = false;
                if($orderVal->transaction_status == 'UNCLAIMED')
                {
                    $data = [
                        'amount' => number_format($orderVal->order_payout_amount, 2), 
                        'order_id' => $orderVal->id, 
                        'payout_batch_id' => '', 
                        'batch_status' =>  '',
                        'payout_item_id' =>  '',
                        'transaction_id' =>  '',
                        'transaction_status' =>  '',
                        'receiver' =>  '' 
                    ];
                    /** Create batch payout **/
                    $payoutRequest = new PayoutsPostRequest();                        
                    $client = PayPalClient::client();
                    $payoutRequest->body = self::buildPayoutData($orderVal->order_payout_amount,$orderVal->currency,$orderVal->product_owner_email);
                    $payoutResponse = $client->execute($payoutRequest);
                    $data['api_response'] = $payoutResponse;
					
					
                    if($payoutResponse->statusCode == 201)
                    {
                        $data['batch_status']       = $payoutResponse->result->batch_header->batch_status;
                        $data['payout_batch_id']    = $payoutResponse->result->batch_header->payout_batch_id;
                        /** Show payout batch details **/
                        $payoutGetRequest           = new PayoutsGetRequest($data['payout_batch_id']);
                        $payoutGetResponse          = $client->execute($payoutGetRequest);
                        $data['api_response']       = $payoutGetResponse;
						Log::info('payoutGetResponse',['res' => $payoutGetResponse]);
						// die('ok');
                        if($payoutGetResponse->statusCode == 200)
                        {
                            $data['batch_status']   = $payoutGetResponse->result->batch_header->batch_status;
                            $data['payout_item_id'] = $payoutGetResponse->result->items[0]->payout_item_id;
                            /** Show payout item details **/
                            $payoutsItemGetRequest  = new PayoutsItemGetRequest($data['payout_item_id']);
                            $payoutsItemGetResponse = $client->execute($payoutsItemGetRequest);
                            $data['api_response']   = $payoutsItemGetResponse;
                            Log::info('payoutsItemGetResponse',['res' => $payoutsItemGetResponse]);    
                            if($payoutsItemGetResponse->statusCode == 200)
                            {                                    
                                $data['transaction_status'] = $payoutsItemGetResponse->result->transaction_status;
                                $data['receiver']           = $payoutsItemGetResponse->result->payout_item->receiver;
                                $orderNeedsToBeCreated = true;
                            }
                        }
                    }
                    
					        $record = OrderPayout::find($orderVal->order_payout_id);
							$data['transaction_id'] = $record->transaction_id;
							$record->update($data);
                    // OrderPayout::create($data);
                    //  if($orderNeedsToBeCreated == true)
                    //  {
                    //      OrderPayout::find($orderVal->order_payout_id)->delete();
                    //  }
				
                }
            }
        } 
		$amount = DB::table('orders')
		->join('order_payouts', 'orders.id', '=', 'order_payouts.order_id')
		->join('users', 'orders.product_owner', '=', 'users.id')
		->select('orders.currency','orders.product_owner','orders.id','order_payouts.id as order_payout_id', 'order_payouts.order_id','order_payouts.amount as order_payout_amount', 'order_payouts.payout_batch_id','order_payouts.batch_status','order_payouts.payout_item_id','order_payouts.transaction_status','users.paypal_email as product_owner_email')
		->where(['order_payouts.transaction_status'=>'UNCLAIMED','orders.product_owner'=>Auth::id()])
		->sum('order_payouts.amount');
		$user=User::find(Auth::id());
		$isPaypalEmailVerified = $user->is_paypal_email_verified;
		return response()->json(['status' => true, 'message' => '', 'amount' => $amount,'isPaypalEmailVerified'=>$isPaypalEmailVerified]);	
	}
		
}
