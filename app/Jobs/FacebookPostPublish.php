<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Facebook\Facebook;

class FacebookPostPublish implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product, $productImg;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product, $productImg)
    {
        //
        $this->product = $product;
        $this->productImg = $productImg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fb = new Facebook([
			'app_id' => env('FACEBOOK_APP_ID'),
			'app_secret' => env('FACEBOOK_APP_SECRET'),
			'default_graph_version' => 'v17.0',
		]);
		
		$response = $fb->post(
			'/104269192748748/photos',
			['message' => "Check out ".$this->product->title." on our website https://alium-gaming.com/product-detail/".$this->product->slug,'url' => $this->productImg],
			'EAAnFN5NG80MBAHZCK0hYzRfMUINrMUXtyRvMOUKSvGBZAZBa2UhNNS6h7LHUCKTNtd4Gh09ex3dX1nMfYp8xr402ZAoOGO4in1WTp61Ya5fYNcFacp0y6UkZAr5MXY18O0ooRVZBnNR7PFPLTtw0pXm1g0SOO3G1ARxFqh9z0nW7LAo1CbeFsa'
		);	
    }
}
