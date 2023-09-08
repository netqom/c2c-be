<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
class Notification extends Model
{
	public function from(){
		return $this->belongsTo(User::class, 'created_by');
	}
	
	public function touser(){
		return $this->belongsTo(User::class, 'user_id');
	}
	
	/** Get the list Of Notification **/
	public function getDataTotalRecords($request)
    { 
		$search       = isset($request['query']['search_string']) ? $request['query']['search_string'] : '';
		$order_colomn = isset($request['sort']['field']) ? $request['sort']['field'] : 'id';
		$sort_type    = isset($request['sort']['sort']) ? $request['sort']['sort'] : 'desc';
	   
	   	$this->where('user_id', Auth::id())->update(['read_status' => 1]);
		$query = $this->where('user_id', Auth::id())
			->with(['from' => function($q){
					return $q->select('id','name');
				},
				'touser' => function($q){
					return $q->select('id','name');
				},
			])
			->when($search != '', function ($q) use ($search) {
				$q->where(function ($query)use ($search) {
					$query->where('notifications.description',  'like',  '%' . $search . '%');
					//$query->where('notifications.created_at',  '=',  '2023-03-23');
				});
			});
		return $query->orderBy($order_colomn, $sort_type);		
    }
	
	public function deleteItem($id)
	{
		$noti = Notification::find($id);
		$noti->delete();
		return true;
	}
}