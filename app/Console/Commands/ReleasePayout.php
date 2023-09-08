<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Stripe\Account;
use Stripe\Stripe;
use App\Jobs\ReleasePayoutJob;



class ReleasePayout extends Command
{
    protected $signature = 'release:payout';

    protected $description = 'Command description';

    public function handle()
    {
        ReleasePayoutJob::dispatch(); 
    }
}
