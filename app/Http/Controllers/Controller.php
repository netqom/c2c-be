<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function prepareData($queryObj, $request)
	{
		$limit  = $request['pagination']['perpage'];
        $offset = ($request['pagination']['page'] -1)*$limit;
		
		$records_count = $queryObj->count();
		$records       = $queryObj->skip($offset)->take($limit)->get();
       
		$meta = [];
		$meta['total']      = $records_count;
		$meta['perpage']    = $request['pagination']['perpage'];
		$meta['pages']      = ceil($records_count/$limit);
		$meta['field']      = 'id';
		$meta['sort']       = 'desc';
		$meta['page']       = $request['pagination']['page'] ;
		$meta['order_types']= $request['order_types'];
		$data['data']       = $records;
		$data['meta']       = $meta;
		
		return $data;
	}
}
