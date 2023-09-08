<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\{User,Product,Notification};
use Auth, DB;
class ReviewRating extends Model
{

	public function user(){
		return $this->belongsTo(User::class, 'created_by');
	}
	
	public function order(){
		return $this->belongsTo(Order::class, 'order_id');
	}
	
	public function product(){
		return $this->belongsTo(Product::class, 'product_id');
	}
	
    /** Create And Update Particular ReviewRating **/
    public function createAndUpdate($request){
    	
    	if($request->item_id == 0){
			$review = new ReviewRating();
			$review->created_by	= Auth::id();
		}else{
			$review = ReviewRating::find($request->item_id);
		}
		$review->order_id   = $request->order_id;
		$review->product_id = $request->product_id;
		$review->rating     = $request->rating;
		$review->review     = $request->review;
		$review->status     = 1;
		$review->updated_by = Auth::id();
		if($review->save()){
			//Update user rating based on current rating
			$this->updateUserRating($request->product_id);
			//add notification for seller
			$this->addSellerNotification($request);
			//add notification for admin
			$this->addAdminNotification($request);
			//Update product rating
			$this->updateProductRating($request->product_id);
			return true;
		}
    	return false;
    }


    public function updateProductRating($product_id)
    {
    	$product = Product::find($product_id);
    	$result = $this->select(DB::raw('count(review_ratings.product_id) as review_count'), DB::raw('sum(review_ratings.rating) as review_total'))->where(['product_id' => $product_id])->first();
    	$product->rating_count = $result->review_count;	
		$product->avg_rating = $result->review_total/$result->review_count;
		$product->save();	
    }
	
	public function updateUserRating($product_id)
	{
		$product = Product::find($product_id);
		$result = Product::where('products.created_by', $product->created_by)
							->select(DB::raw('count(review_ratings.product_id) as review_count'), DB::raw('sum(review_ratings.rating) as review_total'))
							->leftJoin('review_ratings', 'products.id', '=', 'review_ratings.product_id')
							->first();
		$user = User::find($product->created_by);
		$user->rating_count = $result->review_count;	
		$user->avg_rating = $result->review_total/$result->review_count;
		$user->save();	
	}
	
	public function addSellerNotification($request)
	{
		$product = Product::find($request->product_id);
		
		$msg = '';
		if($request->item_id == 0){
			$msg .= Auth::user()->name.' has given a review and rating to the product sold by you';
		}else{
			$msg .= Auth::user()->name.' has updated his previous review and rating to the product sold by you';
		}
		$notification = new Notification();
		$notification->user_id     = $product->created_by;
		$notification->type        = $request->item_id == 0 ? 2 : 3;
		$notification->item_id     = $request->order_id;
		$notification->description = $msg;
		$notification->status      = 1;
		$notification->created_by  = Auth::id();
		$notification->updated_by  = Auth::id();
		$notification->save();
	}
	
	public function addAdminNotification($request)
	{
		$admin_users = User::where(['role' => 1, 'status' => 1])->get();
		$msg = '';
		if($request->item_id == 0){
			$msg .= Auth::user()->name.' has given a review and rating to the product';
		}else{
			$msg .= Auth::user()->name.' has updated his previous review and rating to the product';
		}
		foreach($admin_users as $admin_user){
			$notification = new Notification();
			$notification->user_id     = $admin_user->id;
			$notification->type        = $request->item_id == 0 ? 2 : 3;
			$notification->item_id     = $request->order_id;
			$notification->description = $msg;
			$notification->status      = 1;
			$notification->created_by  = Auth::id();
			$notification->updated_by  = Auth::id();
			$notification->save();
		}
	}
	
	/** Delete ReviewRating **/
	public function deleteItem($id)
	{
		$review = $this->find($id);
		$review->delete();
		return true;
	}
	public function getReviews($request){
		$sellerId = '';
		$product_id = $request->filled('product_id') ? decryptDataId($request->product_id):''; 	
		if ($request->filled('query')) {
		    $sellerId = $request['query']['seller_id'] ? $request['query']['seller_id'] : '';
		}		
        return $this->with('product')->when($product_id!='',function($q) use($product_id){
			    $q->where('product_id',$product_id);
			})
        	->when($sellerId != '' && $sellerId != 0, function ($q) use ($sellerId) {
				$q->whereHas('product', function ($query) use($sellerId) {
					return $query->where('created_by', $sellerId);
				});
			});
	}
}
