<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ProductVideo;
use File;
use Illuminate\Support\Facades\Log;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filePaths;
    public $productData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePaths,$productData)
    {
        $this->filePaths = $filePaths;
        $this->productData = $productData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$folderPath = storage_path('app/public/products/'.$this->productData->id.'/videos');
		if(!File::isDirectory($folderPath)){
		    File::makeDirectory($folderPath, 0777, true, true);
		}
		Log::info('asx',['ss' => $this->filePaths]);
		foreach($this->filePaths as $key => $file){
			$extension  = $file->getClientOriginalExtension();
			$videoName = uniqid(). '.' .$extension;
			$path = $file->storeAs("products/".$this->productData->id."/videos", $videoName,'public'); // Change 'public' to your disk name if necessary.
			$video = new ProductVideo();
			$video->product_id = $this->productData->id;
			$video->video_type = $extension;
			$video->name       = $videoName;
			$video->size       = $file->getSize();
			$video->video_path = $path;
			$video->save();
		}
    }
}
