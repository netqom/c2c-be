<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Facebook\Facebook;

class InstagramPostPublish implements ShouldQueue
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
			'app_id' => '828329605356958',
			'app_secret' => '59773c7dd9c0c33d60f7c129821557f3',
			'default_graph_version' => 'v17.0',
		]);
		
		$response = $fb->post(
			'/17841452638580063/media',
			['caption' => "Check out ".$this->product->title." on our website. Please copy this link: https://alium-gaming.com/product-detail/".$this->product->slug,'image_url' => $this->productImg],
			'EAALxXIPiXZA4BAIrEaPU7eIogwxK5tXugrNKYozwzoKTd3ZBzqmJZB4ZBVDfUGwQLKUZBslNUMZC0ZAGC8ZAxZCHQufLZAufbDR0nMVwaq1coKSfRqJBnHhIsfJjW7C3354f2rkExRTuZCqYErZCR26p8NQWwAJvZBZBUdzrgJ1ay4D13wVhW7r2T3WVZCb'
		);	

		$graphNode = $response->getGraphNode();
		$creation_id = $graphNode['id'];

		$response = $fb->post(
			'/17841452638580063/media_publish',
			['creation_id' => $creation_id],
			'EAALxXIPiXZA4BAIrEaPU7eIogwxK5tXugrNKYozwzoKTd3ZBzqmJZB4ZBVDfUGwQLKUZBslNUMZC0ZAGC8ZAxZCHQufLZAufbDR0nMVwaq1coKSfRqJBnHhIsfJjW7C3354f2rkExRTuZCqYErZCR26p8NQWwAJvZBZBUdzrgJ1ay4D13wVhW7r2T3WVZCb'
		);	
    }
}
