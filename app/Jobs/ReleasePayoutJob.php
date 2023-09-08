<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use App\Models\{OrderPayout};

class ReleasePayoutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderPayouts;

    protected $customLog;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(OrderPayout $orderPayouts)
    {

        $this->customLog = Log::channel('connect_payout');

        $this->orderPayouts = $orderPayouts;

        $this->customLog->info('Users payout started at : '. date('Y-m-d H:i:s'));
        
        $payoutInterval = 10;

        $orderPayouts = $this->orderPayouts->getUnpaidPayout()->get();
        
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        foreach ($orderPayouts as $key => $orderPayout) {
                
            if($orderPayout->order->status == 'succeeded' || $orderPayout->order->status == 'processing'){
                
                if(!is_null($orderPayout->order->connect_account) ){

                    try {
                        $transfer = $stripe->transfers->create([
                            'amount' => floor(($orderPayout->amount) * (100)),
                            'currency' => 'gbp',
                            'destination' => $orderPayout->order->connect_account->account_id,
                        ]);
                        if($transfer->id){

                            $this->orderPayouts->where('id',$orderPayout->id)->update([
                                'transfer_id'           => $transfer->id,
                                'amount_reversed'       => $transfer->amount_reversed,
                                'destination'           => $transfer->destination,
                                'destination_payment'   => $transfer->destination_payment,
                                'source_type'           => $transfer->source_type,
                                'transaction_status'    => 'SUCCESS',
                                'api_response'          => $transfer
                            ]);
                        }

                    }catch(\Stripe\Exception\CardException $e) {
                        $this->customLog->error($e->getMessage());
                        $this->saveApiResponse($e->getMessage(), $orderPayout);
                    } catch (\Stripe\Exception\RateLimitException $e) {
                        $this->customLog->error($e->getMessage());
                        $this->saveApiResponse($e->getMessage(), $orderPayout);
                    } catch (\Stripe\Exception\InvalidRequestException $e) {
                        $this->customLog->error($e->getMessage());
                        $this->saveApiResponse($e->getMessage(), $orderPayout);
                    } catch (\Stripe\Exception\AuthenticationException $e) {
                        $this->customLog->error($e->getMessage());
                        $this->saveApiResponse($e->getMessage(), $orderPayout);
                        $this->customLog->error($e->getMessage());
                        $this->saveApiResponse($e->getMessage(), $orderPayout);
                    } catch (\Stripe\Exception\ApiConnectionException $e) {
                        $this->customLog->error($e->getMessage());
                        $this->saveApiResponse($e->getMessage(), $orderPayout);
                    } catch (\Stripe\Exception\ApiErrorException $e) {
                        $this->customLog->error($e->getMessage());
                        $this->saveApiResponse($e->getMessage(), $orderPayout);
                    } catch (Exception $e) {
                        $this->customLog->error($e->getMessage());
                        $this->saveApiResponse($e->getMessage(), $orderPayout);
                    }

                }
                $this->customLog->info('running transfer api : '. date('Y-m-d H:i:s'));
            }

        }
    }


    public function saveApiResponse($response,$orderPayout){
        $this->customLog->info('api_response'. $response);
        $this->orderPayouts->where('id',$orderPayout->id)->update(['api_response' => $response ]);
    }
}
