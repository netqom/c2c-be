<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\TryToCompleteOrderJob;
use DB;

class RetryToCompleteOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retry:complete-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Try to complete the order that was not completed in first attempt';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TryToCompleteOrderJob::dispatch();   
    }
}
