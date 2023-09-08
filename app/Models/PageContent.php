<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Auth, Str, Storage, DB;
use Carbon\Carbon;

class PageContent extends Model
{
    public $timestamps = true;
    
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'id');
    }

    public function deleteBannerImages($id)
	{
		$images = PageContent::where('page_id',$id)->where('param','banner_image');
		//first delete old image
       
	   foreach($images->get() as $image){	
    //    print_r($image->value);
       $storagePath = Storage::disk('public')->path($image->value);
        if(Storage::disk('public')->exists($image->value)){
			Storage::disk('public')->deleteDirectory($image->value);
          unlink($storagePath);
		}
		$image->delete();
       }
		return true;
	}
    public function deletePageImage($id)
	{
		$image = PageContent::find($id);
	    $storagePath = Storage::disk('public')->path($image->value);
        if(Storage::disk('public')->exists($image->value)){
			Storage::disk('public')->deleteDirectory($image->value);
          unlink($storagePath);
		}
		$image->delete();
       
		return true;
	}
	
	
}    