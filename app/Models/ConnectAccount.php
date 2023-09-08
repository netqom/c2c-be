<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Mail;
use Illuminate\Support\Facades\Crypt;

class ConnectAccount extends Model
{
    use HasFactory;

    public function saveConnectAccount($data){


        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $accountDetail= $stripe->accounts->create([
            'type' => 'custom',
            'business_type' => 'individual',
            'email' => Auth::user()->email,
            'country' => 'GB',
            //'tos_acceptance' => ['service_agreement' => 'recipient'],
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ]
            ,
             'external_account' => [
                 'object' => 'bank_account',
                 'account_number' => $data->account_number,
                //  'account_number' => 'GB82WEST12345698765432',
                 //'routing_number' => '108800',
                 'country' => 'GB', // Update with the appropriate country
                 'currency' => 'gbp', // Update with the appropriate currency
             ],
        ]);

        $this->where('user_id',Auth::user()->id)->delete();

        $account= new ConnectAccount();
        $account->user_id        = Auth::user()->id;
        $account->account_id     = $accountDetail->id;
        $account->account_status = "unverified";
        $account->save();

        $refreshUrl  = env('FRONTEND_APP_URL').'/auth/profile#account_info';
        $returnUrl =  env('FRONTEND_APP_URL').'/auth/profile?response_id='.Crypt::encryptString(Auth::user()->id).'#account_info';

        $stripeLink = $stripe->accountLinks->create([
            'account' => $accountDetail->id,
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
            'type' => 'account_onboarding',
            'collect' => 'eventually_due',
        ]);
    
        return $stripeLink;
    }

    public function updateConnectAccount($data){
        $account= $this->where('user_id', Crypt::decryptString($data->response_id))->first();
        if($account){
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $accountDetail = $stripe->accounts->retrieve($account->account_id);
            if($accountDetail->business_type == 'company'){
                $account->account_status    = isset($accountDetail->company->verification->status) ? $accountDetail->company->verification->status : 'unverified';
            }else{
                $account->account_status    = $accountDetail->individual->verification->status;
            }
            
            $account->account_verify_at = date('Y-m-d H:i:s');
            $account->updated_at        = date('Y-m-d H:i:s');
            $account->save();
            $msg = 'Your connect account created successfully';
            if($account->account_status != 'verified'){
                $msg .= '. But your account is not verfied please click the link to verify your account';
            }else{
                $msg .= ' and verified. Now you are all set to receive the payment';
            }
            $email_data['subject'] = 'Connect Account Created';
            $email_data['email']   = Auth::user()->email;
            $email_data['name']    = Auth::user()->name;
            $email_data['user']    = 'Buyer';
            $email_data['msg']     = $msg;
            $email_data['url']     = env('FRONTEND_APP_URL');
            dispatch(new \App\Jobs\CreateConnectAccountJob($email_data));
            return $email_data;
        }        
        return '';
    }

    /*** verify connect acccount ***/
    public function verifyConnectAccount($request){
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));         
        $accountDetail = ConnectAccount::where('user_id', Auth::user()->id)->first();
        if($accountDetail){           
            $refreshUrl  = env('FRONTEND_APP_URL').'/auth/profile#account_info';
            $returnUrl = env('FRONTEND_APP_URL').'/auth/profile?response_id='.Crypt::encryptString(Auth::user()->id).'#account_info';              
            $stripeLink = $stripe->accountLinks->create([
                'account' => $accountDetail->account_id,
                'refresh_url' => $refreshUrl,
                'return_url' => $returnUrl,
                'type' => 'account_onboarding',
                'collect' => 'eventually_due',
            ]); 
            
            return $stripeLink;
        }
    }

    public function addMoreAct($data,$conAccData){
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $accountDetail = $stripe->accounts->createExternalAccount($conAccData['account_id'],[
            'external_account' => [
                'object' => 'bank_account',
                'account_number' => 'GB82WEST12345698765432',
                //'routing_number' => '108800',
                'country' => 'GB', // Update with the appropriate country
                'currency' => 'gbp', // Update with the appropriate currency
            ],
        ]);

        $account= new ConnectAccount();
        $account->user_id        = Auth::id();
        $account->account_id     = $accountDetail->id;
        $account->account_status = "unverified";
        $account->is_default     = 1;
        $account->save();
        return $accountDetail;
    }
}
