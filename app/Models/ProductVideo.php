<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class ProductVideo extends Model
{
	
	protected $appends = ['url'];
	
	public function getUrlAttribute() 
	{ 
		if(!is_null($this->video_path) && $this->video_path != ''){
			return url('storage/'.$this->video_path);
		}else{
			return '';
		}
	}

	
	
	public function deleteItem($id)
	{
		$video = $this->find($id);
		//first delete old video
		if(Storage::disk('public')->exists($video->video_path)){
			Storage::disk('public')->delete($video->video_path);
		}
		$video->delete();
		return true;
	}

}