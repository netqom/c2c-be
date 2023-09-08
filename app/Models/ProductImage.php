<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;
use Illuminate\Support\Facades\{Lang,Log};

class ProductImage extends Model
{
	
	protected $appends = ['url','thumb_url'];
	
	public function getUrlAttribute() 
	{ 
		if(!is_null($this->image_path) && $this->image_path != ''){
			return url('storage/'.$this->image_path);
		}else{
			return '';
		}
	}

	public function getThumbUrlAttribute() 
	{ 
		if(!is_null($this->thumb_path) && $this->thumb_path != ''){
			return url('storage/'.$this->thumb_path);
		}else{
			return '';
		}
	}
	
	public function deleteItem($id)
	{
		$image = ProductImage::find($id);
		//first delete old image
		if(Storage::disk('public')->exists($image->image_path)){
			Storage::disk('public')->delete($image->image_path);
			Storage::disk('public')->delete($image->thumb_path);
		}
		$image->delete();
		return true;
	}

}