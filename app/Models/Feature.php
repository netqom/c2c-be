<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Page};
use Auth, Str, Storage, DB;
use Carbon\Carbon;

class Feature extends Model
{
    public $timestamps = true;
    protected $fillable = ['image','description'];
   
    public function createUpdateItem($request)
	{
		if($request->item_id == 0){
			$feature = new Feature();
		}else{
			$feature = Feature::find($request->item_id);
		}
		$page = Page::where('slug','feature')->first();
        if(!$page){
          return false;
        }
        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename   = $file->getClientOriginalName();
            $extension  = $file->getClientOriginalExtension();
            $image_name = date('mdYHis') . uniqid(). '.' .$extension;
            Storage::disk('public')->putFileAs('pages/'.$page->id.'/', $file, $image_name);
            
            $image['page_id'] = $page->id;
            $image['image_type'] = $extension;
            $image['name']       = $image_name;
            $image['size']       = Storage::disk('public')->size('pages/'.$page->id.'/'.$image_name);
            $image['image_path'] = 'pages/'.$page->id.'/'.$image_name; 
        }
		$feature->image    = isset($image['image_path']) ? $image['image_path'] : $feature->image;
		$feature->description      = $request->description;
	
  		$feature->save();
        return $feature;
	} 
   
}  