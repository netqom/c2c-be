<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Auth, Str, Storage, DB;
use Carbon\Carbon;

class Faq extends Model
{
    public $timestamps = true;
    protected $fillable = ['question','answer'];
    public function createUpdateItem($request)
	{
		if($request->item_id == 0){
			$faq = new Faq();
		}else{
			$faq = Faq::find($request->item_id);
		}
		
		$faq->question    =$request->question;
		$faq->answer      = $request->answer;
	
  		$faq->save();
        return $faq;
	}
}   