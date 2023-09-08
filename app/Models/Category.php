<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth, Str, Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $appends = ['parent_name', 'display_path'];
	
	
	public function getParentNameAttribute()
	{
		$name = '';
		if($this->parent_id != 0){
			$parent = $this->find($this->parent_id);
			$name = $parent->name;
		}
		return $name;
	}
	
	public function getDisplayPathAttribute() 
	{
		if(!is_null($this->display_image) && $this->display_image != ''){
			return url('storage/categories/'.$this->display_image);
		}else{
			return '';
		}
	}
	
	public function product()
	{
		return $this->hasMany('App\Models\Product', 'category_id');
	}
	
	public function getDataTotalRecords($request)
    { 
	   $search       = isset($request['query']['search_string']) ? $request['query']['search_string'] : '';
	   $order_colomn = isset($request['sort']['field']) ? $request['sort']['field'] : 'id';
	   $order_type   = isset($request['sort']['sort']) ? $request['sort']['sort'] : 'desc';
	   
       
	 return $this->when($search != '', function ($query) use ($search) {
                 return $query->where('name',  'like',  '%' . $search . '%');
            })
			->when($order_colomn != '', function ($query) use ($order_colomn, $order_type) {
				if($order_colomn != 'action' &&  $order_colomn != 'image'){
					 return $query->orderBy($order_colomn, $order_type);
				}else{
					 return $query->orderBy('id', 'desc');
				}
            })
            ->select('categories.*');
    }
	
	public function createItem($request)
	{
        if ($request->item_id > 0) {
            $category = Category::find($request->item_id);
        } else {
            $category = new Category();
			$category->created_by = Auth::user()->id;
        }
        
		$category->parent_id  = $request->parent_id != '' ? $request->parent_id : 0;
		$category->name       = $request->name;
		$category->slug       = Str::slug($request->name);
        $category->updated_by = Auth::user()->id;
		$category->status     = 1;
		$category->save();
		//save display image
		$display = $this->saveDisplayImage($request, $category);
		return true;		
	}
	
	public function saveDisplayImage($request, $category)
	{
		if ($request->file('display_image')) {
			//first delete old image
			Log::info('printing',['sssssssss' => Storage::disk('public')->exists('categories/'.$category->display_image)]);
			//Log::info('User failed to login.', ['id' => $user->id]);
			if(Storage::disk('public')->exists('categories/'.$category->display_image)){
				Storage::disk('public')->delete('categories/'.$category->display_image);
			}
			$image = $request->file('display_image');
			$fileType = $image->GetClientOriginalExtension();
			$display = 'display_'.time() . '.' . $fileType;
			//save display image
			Storage::disk('public')->putFileAs('categories/', $image, $display);
			//update image detail in db
			$category->display_image = $display;
			$category->save();
			return $display;
		}
	}
	
	public function deleteItem($id)
	{
		Category::find($id)->delete();
		return true;
		$category = Category::find($id);
		//first delete old image
		if(Storage::disk('public')->exists('categories/'.$category->display_image)){
			Storage::disk('public')->delete('categories/'.$category->display_image);
		}
		
		$category->delete();
		return true;
	}
}