<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Category,ConnectAccount,Country,Notification,Order,OrderPayout,Page,Product,ReviewRating,UkCity,UkState,User};
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log,Lang};
use Stripe\Refund;

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

    public function __construct(ConnectAccount $connectAccount, Page $pageModel, OrderPayout $orderPayout, ReviewRating $reviewRating, Notification $notifications, User $user, Country $country, Product $product, Order $order, Category $category, UkState $ukState, UkCity $ukCity)
    {
        $this->user = $user;
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

    /** Payment Refund to users **/

    public function refundPayment(Request $request)
    {

        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            // Create a PaymentIntent with amount and currency
            $paymentRefund = $stripe->refunds->create([
                'payment_intent' => $request->payment_intent_id,
                'amount' => ((int) ($request->amount) * 100),
            ]);

            if ($paymentRefund->status == 'succeeded') {
                $updateOrderData = $this->order->updatePaymentRefundData($paymentRefund);
                // return response()->json(['type' => 'success', 'msg' => Lang::get('auth.productRemove')]);
                if ($updateOrderData) {
                    return response()->json(['type' => 'success', 'msg' => 'Payment Refunded Successfully']);
                } else {
                    return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError')]);
                }
            } else {
                return response()->json(['type' => 'success', 'msg' => 'Payment Refunded Cancelled']);
            }

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Handle invalid request exceptions
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Handle authentication exceptions
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Handle API connection exceptions
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle generic API errors
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // /*** Add Stripe Connetc Account ****/
    // public function createConnectAccount(Request $request){

    //     $validator = Validator::make($request->all(),['account_number' => ['required']]);
    //     if($validator->fails()){
    //         return response()->json(['status' => false,  'message' => $validator->errors()->first()]);
    //     }

    //     try{
    //         $stripeLink = $this->connectAccount->saveConnectAccount($request);
    //         if($stripeLink->url){
    //             return response()->json(['status' => true, 'message' => 'Connect account created successfully.', 'data'=> $stripeLink ]);
    //         }
    //     } catch(\Stripe\Exception\CardException $e) {
    //         return response()->json(['status' => false, 'message' => "A payment error occurred: {$e->getError()->message}" ]);
    //     } catch (\Stripe\Exception\InvalidRequestException $e) {
    //         return response()->json(['status' => false, 'message' => $e->getError()->message]);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => false, 'message' => $e->getError()->message ]);
    //     }

    // }

    // /** Get List Of Connected Account (Stripe) **/
    // public function getConnectedAccounts(Request $request){
    //     $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    //     $conAccData = $this->connectAccount->where('user_id', Auth::id())->latest()->first();
    //     if(!empty($conAccData)){
    //         try{
    //             $accounts = $stripe->accounts->retrieve($conAccData['account_id'], []);
    //             if(isset($accounts->id)){
    //                 return response()->json(['status' => true , 'data' => $accounts]);
    //             }
    //         } catch(\Stripe\Exception\CardException $e) {
    //             return response()->json(['status' => false, 'message' => "A payment error occurred: {$e->getError()->message}" ]);
    //         } catch (\Stripe\Exception\InvalidRequestException $e) {
    //             return response()->json(['status' => false, 'message' => $e->getError()->message]);
    //         } catch (\Exception $e) {
    //             return response()->json(['status' => false, 'message' => $e->getError()->message ]);
    //         }
    //     }
    //     return response()->json(['status' => false, 'message' => 'You did not have any connect account yet.' ]);
    // }

    // /** Update connect account according to getting response **/
    // public function updateConnectAccount(Request $request){
    //     if (Auth::check()) {
    //         try{
    //             $data = $this->connectAccount->updateConnectAccount($request);
    //             if($data != ''){
    //                 return response()->json(['status' => true, 'message' => $data['msg']]);
    //             }
    //             return response()->json(['status' => false, 'message' => 'You did not have any connect account yet.']);
    //         } catch (\Stripe\Exception\InvalidRequestException $e) {
    //             return response()->json(['status' => false, 'message' => $e->getError()->message]);
    //         } catch (\Exception $e) {
    //             return response()->json(['status' => false, 'message' => $e->getError()->message ]);
    //         }
    //     }
    //     return response()->json(['status' => false, 'message' => 'Please login first.' ]);
    // }

    // /*** Create Payment Intent On Page Load ***/
    // public function createPaymentIntent(Request $request){

    //     try {
    //         $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    //         // Create a PaymentIntent with amount and currency
    //         $paymentIntent = $stripe->paymentIntents->create([
    //             'amount' => $request->amount,
    //             'currency' => $request->currency
    //         ]);

    //         $output = [
    //             'clientSecret' => $paymentIntent->client_secret,
    //         ];

    //         return response()->json(['status' => true, 'data' => $output ]);

    //     } catch (Error $e) {
    //         return response()->json(['status' => false, 'message' => $e->getMessage() ]);
    //     }
    // }

    // /** Verify connect account **/
    // public function verifyConnectAccount(Request $request)
    // {
    //     if (Auth::check()) {
    //         try {
    //             $stripeLink = $this->connectAccount->verifyConnectAccount($request);
    //             if($stripeLink->url){
    //                 return response()->json(['status' => true, 'message' => 'Connect account created successfully.', 'data'=> $stripeLink ]);
    //             }
    //         } catch(\Stripe\Exception\CardException $e) {
    //             return response()->json(['status' => false, 'message' => "A payment error occurred: {$e->getError()->message}" ]);
    //         } catch (\Stripe\Exception\InvalidRequestException $e) {
    //             return response()->json(['status' => false, 'message' => $e->getError()->message]);
    //         } catch (\Exception $e) {
    //             return response()->json(['status' => false, 'message' => $e->getError()->message ]);
    //         }
    //     }
    //     return response()->json(['status' => false, 'message' => 'Please login first.' ]);
    // }

}