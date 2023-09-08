<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ConnectAccount,Country,User,Product,Order,Category,Notification,ReviewRating,OrderPayout,Page,UkState,UkCity};
use Illuminate\Support\Facades\{Lang,Validator,Hash,Crypt,Log};
use Auth;
use Carbon\Carbon;
use App\Mail\SendMail;
use Mail;
use Stripe\{Stripe,Account,Event};

class StripePaymentController extends Controller
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
    }

    public function updateOrderWebhook(Request $request){
        // Parse the incoming event.
        $event = Event::constructFrom(json_decode($request->getContent(), true));
        $paymentIntent = $event->data->object;
        $this->order->where(['payment_intent_id' => $paymentIntent->id])->update(['status' => $paymentIntent->status]);
    }

    
    /*** Add Stripe Connetc Account ****/
    public function createConnectAccount(Request $request){

        $validator = Validator::make($request->all(),['account_number' => ['required']]);
        if($validator->fails()){
            return response()->json(['status' => false,  'message' => $validator->errors()->first()]);
        }  

        try{
            $stripeLink = $this->connectAccount->saveConnectAccount($request);    
            if($stripeLink->url){
                return response()->json(['status' => true, 'message' => 'Connect account created successfully.', 'data'=> $stripeLink ]);
            }
        } catch(\Stripe\Exception\CardException $e) {
            return response()->json(['status' => false, 'message' => "A payment error occurred: {$e->getError()->message}" ]);        
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json(['status' => false, 'message' => $e->getError()->message]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getError()->message ]);
        }      
        
    }

    /** Get List Of Connected Account (Stripe) **/
    public function getConnectedAccounts(Request $request){
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $conAccData = $this->connectAccount->where('user_id', Auth::id())->latest()->first();    
        if(!empty($conAccData)){
            try{
                $accounts = $stripe->accounts->retrieve($conAccData['account_id'], []);
                if(isset($accounts->id)){
                    return response()->json(['status' => true , 'data' => $accounts]);
                }
            } catch(\Stripe\Exception\CardException $e) {
                return response()->json(['status' => false, 'message' => "A payment error occurred: {$e->getError()->message}" ]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                return response()->json(['status' => false, 'message' => $e->getError()->message]);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getError()->message ]);
            }
        }     
        return response()->json(['status' => false, 'message' => 'You did not have any connect account yet.' ]);
    }

    /** Update connect account according to getting response **/
    public function updateConnectAccount(Request $request){
        if (Auth::check()) {
            try{
                $data = $this->connectAccount->updateConnectAccount($request); 
                if($data != ''){
                    return response()->json(['status' => true, 'message' => $data['msg']]);
                }   
                return response()->json(['status' => false, 'message' => 'You did not have any connect account yet.']);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                return response()->json(['status' => false, 'message' => $e->getError()->message]);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e ]);
            }      
        }
        return response()->json(['status' => false, 'message' => 'Please login first.' ]);
    }

    /*** Create Payment Intent On Page Load ***/
    public function createPaymentIntent(Request $request){
        
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            // Create a PaymentIntent with amount and currency
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $request->amount,
                'currency' => $request->currency
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            return response()->json(['status' => true, 'data' => $output ]);

        } catch (Error $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage() ]);
        }
    }

    /** Verify connect account **/
    public function verifyConnectAccount(Request $request)
    {
        if (Auth::check()) {
            try {
                $stripeLink = $this->connectAccount->verifyConnectAccount($request);
                if($stripeLink->url){
                    return response()->json(['status' => true, 'message' => 'Connect account created successfully.', 'data'=> $stripeLink ]);
                } 
            } catch(\Stripe\Exception\CardException $e) {
                return response()->json(['status' => false, 'message' => "A payment error occurred: {$e->getError()->message}" ]);       
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                return response()->json(['status' => false, 'message' => $e->getError()->message]);
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getError()->message ]);
            }  
        }
        return response()->json(['status' => false, 'message' => 'Please login first.' ]);
    }

}
