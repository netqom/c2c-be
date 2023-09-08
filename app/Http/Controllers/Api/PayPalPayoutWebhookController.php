<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\WebhookEvent;
use PayPal\Api\VerifyWebhookSignatureResponse;
use App\Models\{OrderPayout};
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client as GuzzleClient;

class PayPalPayoutWebhookController extends Controller
{

    public function getAccessToken($clientId,$clientSecret){

        $client = new Client();

        try {
            $response = $client->request('POST', 'https://api-m.sandbox.paypal.com/v1/oauth2/token', [
                'auth' => [$clientId, $clientSecret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $responseData = json_decode($body, true);
            $accessToken = $responseData['access_token'];

            return ['success' => true, 'accessToken' => $accessToken];

        } catch (RequestException $e) {
            
            $statusCode = $e->getResponse()->getStatusCode();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    public function verifyWebhookEvent($bodyData,$headersData,$accessToken,$webhookId){
       
        $headersDatax = json_decode(json_encode($headersData),true);
        
        $data = [
            "webhook_id"        => $webhookId,
            "transmission_id"   => $headersDatax["paypal-transmission-id"][0],
            "transmission_time" => $headersDatax['paypal-transmission-time'][0],
            "cert_url"   =>     $headersDatax['paypal-cert-url'][0],
            "auth_algo"  => $headersDatax['paypal-auth-algo'][0],
            "transmission_sig"  => $headersDatax['paypal-transmission-sig'][0],
            "webhook_event"     => json_decode($bodyData)
        ];
        $body = json_encode($data);
        //
        $headers = [
            'Content-Type' => 'application/json',
            //'AccessToken' => 'key',
            'Authorization' => 'Bearer '.$accessToken
        ];
        $client = new Client([
                 'headers' => $headers
             ]);

        $r = $client->request('POST', 'https://api-m.sandbox.paypal.com/v1/notifications/verify-webhook-signature', [
            'body' => $body
        ]);
       return $response = $r->getBody()->getContents();
    }

    public function handleWebhook(Request $request)
    {  
        Log::info('request',['request' => $request->all()]);
        
        $clientId = config('services.paypal.client_id');

        $clientSecret = config('services.paypal.secret');
        
        $webhookId = config('services.paypal.webhook_id');

        $bodyData = json_decode($request->getContent(), true);
        Log::info('bodyDatag',['bodyDatag' => $bodyData]);
        $headersData = $request->headers->all();
      
        $authResponse = $this->getAccessToken($clientId,$clientSecret);
        
        $verifyRes = $this->verifyWebhookEvent($request->getContent(),$headersData,$authResponse['accessToken'],$webhookId);
        Log::info('verifyRes',['verifyRes' => $verifyRes]);
        $resp = json_decode($verifyRes);
       
        if($resp->verification_status == 'SUCCESS'){

            try {
        
                // Get the event type
                $eventType = $bodyData['event_type'];
                $payoutItemId = $bodyData['resource']['payout_item_id'];
                $payoutBatchId = $bodyData['resource']['payout_batch_id'];
                $transactionStatus = $bodyData['resource']['transaction_status'];

                $eventTypeArray = [
                    'PAYMENT.PAYOUTS-ITEM.SUCCEEDED','PAYMENT.PAYOUTS-ITEM.FAILED','PAYMENT.PAYOUTS-ITEM.RETURNED',
                    'PAYMENT.PAYOUTS-ITEM.BLOCKED','PAYMENT.PAYOUTS-ITEM.CANCELED','PAYMENT.PAYOUTS-ITEM.DENIED',
                    'PAYMENT.PAYOUTS-ITEM.HELD','PAYMENT.PAYOUTS-ITEM.REFUNDED','PAYMENT.PAYOUTS-ITEM.UNCLAIMED'
                ];
                if (in_array($eventType,$eventTypeArray)){
                    $this->saveOrderPayout($payoutItemId,$payoutBatchId,$transactionStatus);
                }
                return response()->json(['success' => true]);

            } catch (\Exception $e) {
                
                Log::info('Webhook response',['error' => $e->getMessage()]);
                return response()->json(['error' => $e->getMessage()], 400);
            
            }
        }

        
    }

    

    public function saveOrderPayout($payoutItemId,$payoutBatchId,$transactionStatus){
        return OrderPayout::where(['payout_item_id' => $payoutItemId])->update(['transaction_status' => $transactionStatus, 'payout_batch_id' => $payoutBatchId]);
    }
}
