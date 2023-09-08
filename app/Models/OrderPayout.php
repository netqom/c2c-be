<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\{Product,OrderPayout,Order,Notification,Setting};
use Auth;
use Illuminate\Support\Facades\Log;
class OrderPayout extends Model
{
    use HasFactory;

     protected $fillable = ['order_id','amount','payout_batch_id','batch_status','payout_item_id','transaction_id','transaction_status','receiver'];
     
    public function order(){
		return $this->belongsTo(Order::class, 'order_id','id');
	}

    /** Create And Update Particular Order Payout **/
    public function createAndUpdatePayoutByStripe($order){
        $productDetail = Product::find($order->product_id);
        

        $orderPayout = OrderPayout::where('order_id',$order->id)->first();
        $amountThatNeedToPayPayee = ( $order->amount ) - ( $order->admin_commission_value );
        if(empty($orderPayout)){
            $orderPayout = new OrderPayout();
            $orderPayout->order_id        = $order->id;
            $orderPayout->amount          = $amountThatNeedToPayPayee;
        }
        if($orderPayout->save()){
            // $notification = new Notification();
            // $notification->user_id     = $order->product_owner;
            // $notification->type        = 1;
            // $notification->item_id     = $order->id;
            
            // $notification->description = "Your product (".$productDetail->title.") has been purchased by ".Auth::user()->name.". To check sold product click on following link <a href=".env("FRONTEND_APP_URL")."/auth/sold-product/>Click here</a>";
                                
            // $notification->status      = 1;
            // $notification->created_by  = Auth::id();
            // $notification->updated_by  = Auth::id();
            // $notification->save();
            return $orderPayout;
        }
        return false;
    }

    /** Get Unpaid Payout For Stripe Based **/
    public function getUnpaidPayout(){
        return $this->with([
            'order' => function($orderQuery){
                return $orderQuery->with([
                    'connect_account' => function($conAcctQur){
                        return $conAcctQur->select('id','user_id','account_id','account_status');
                    }]
                )->select('id','product_owner','status','delivery_price','price','currency');
            }]
        )
        ->where(['transfer_id' => NULL,'payout_batch_id' => NULL, 'payout_item_id' => NULL])
        ->select('id','order_id','amount');
    }

    /** Create And Update Particular Order Payout **/
    public function createAndUpdatePayout($data){
        $order = Order::find($data->order_id);
        $productDetail = Product::find($order->product_id);
        //Log::info('orderdata',['orderdata' => $data]);

        $orderPayout = OrderPayout::where('order_id',$data->order_id)->first();
        if(empty($orderPayout)){
            $orderPayout = new OrderPayout();
            $orderPayout->order_id        = $data->order_id;
            $orderPayout->amount          = $data->amount;
            $orderPayout->payout_batch_id = $data->payout_batch_id;
            $orderPayout->batch_status    = $data->batch_status;
            $orderPayout->payout_item_id  = $data->payout_item_id;
            $orderPayout->receiver        = $data->receiver;
            $orderPayout->transaction_id  = $data->transaction_id;
        }
        $orderPayout->transaction_status  = $data->transaction_status;
        if($orderPayout->transaction_status=='UNCLAIMED'){
            $notification = new Notification();
            $notification->user_id     = $order->product_owner;
            $notification->type        = 6;
            $notification->item_id     = $order->id;
            
            $notification->description = "Your product (".$productDetail->title.") has been purchased by ".Auth::user()->name.". To claim your amount click on following link <a href=".env("FRONTEND_APP_URL")."/auth/sold-product/>Click here</a>";
            					
            $notification->status      = 1;
            $notification->created_by  = Auth::id();
            $notification->updated_by  = Auth::id();
            $notification->save();
        }

        $orderPayout->batch_status        = $data->batch_status;
        $orderPayout->api_response        = $data;
        if($orderPayout->save()){
            return $orderPayout;
        }
        return false;
    }

}
