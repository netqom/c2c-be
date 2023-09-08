<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\{Order,OrderPayout};
use App\Classes\PayPalClient;
use PaypalPayoutsSDK\Payouts\PayoutsItemGetRequest;
use PaypalPayoutsSDK\Payouts\PayoutsPostRequest;
use PaypalPayoutsSDK\Payouts\PayoutsGetRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\OrdersController;
use DB;

class TryToCompleteOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $ordersDetail = DB::table('orders')
            ->join('order_payouts', 'orders.id', '=', 'order_payouts.order_id')
            ->join('users', 'orders.product_owner', '=', 'users.id')
            ->select('orders.currency','orders.product_owner','orders.id','order_payouts.id as order_payout_id', 'order_payouts.order_id','order_payouts.amount as order_payout_amount', 'order_payouts.payout_batch_id','order_payouts.batch_status','order_payouts.payout_item_id','order_payouts.transaction_status','users.paypal_email as product_owner_email')
            ->get();
        
        // Log::info($ordersDetail);
        
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
                        
                        if($payoutGetResponse->statusCode == 200)
                        {
                            $data['batch_status']   = $payoutGetResponse->result->batch_header->batch_status;
                            $data['payout_item_id'] = $payoutGetResponse->result->items[0]->payout_item_id;
                            /** Show payout item details **/
                            $payoutsItemGetRequest  = new PayoutsItemGetRequest($data['payout_item_id']);
                            $payoutsItemGetResponse = $client->execute($payoutsItemGetRequest);
                            $data['api_response']   = $payoutsItemGetResponse;

                            if($payoutsItemGetResponse->statusCode == 200)
                            {                                    
                                $data['transaction_status'] = $payoutsItemGetResponse->result->transaction_status;
                                $data['receiver']           = $payoutsItemGetResponse->result->payout_item->receiver;
                                $orderNeedsToBeCreated = true;
                            }
                        }
                    }
                    Log::info('response',['res' => $data]);
                    OrderPayout::create($data);
                    if($orderNeedsToBeCreated == true)
                    {
                         OrderPayout::find($orderVal->order_payout_id)->delete();
                    }
                }
            }
        }
    }
}
