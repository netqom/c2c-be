<?php
use App\Models\User;

//have this file loaded in composer autoload->files
if (! function_exists('admin_url')) {
	function admin_url($string = '')
	{	
		return  $string == '' ? url('admin') : url('admin/'.$string);
	}
}

    

if (! function_exists('checkactivesection')) {
	function checkactivesection($string, $position = false, $status = 'open')
	{

		if ($position !== false)
			return Request::segment($position) == $string ? $status : '';
		else
			return in_array($string, Request::segments()) ? $status : '';
	}
}

/**
 * check the url if is the current path or at a specified position in array
 * @param string $string
 */
if (! function_exists('checkactivepage')) {
	function checkactivepage($string, $position = false, $status = 'active')
	{
		if ($position !== false)
			return Request::segment($position) == $string ? $status : '';
		else
			return in_array($string, Request::segments()) ? $status : '';
	}
}
    /*
     * To encrypt the id
    */
    function encryptDataId($id = null) {
        if ($id) {
            return Crypt::encryptString($id);
        }
        return false;
    }

    /*
     * To decrypt the id
    */
    function decryptDataId($encrypted_string = null) {
        if ($encrypted_string) {
            return Crypt::decryptString($encrypted_string);
        }
        return false;
    }
    function getRatingStars($avg){
        $html = '';
        $avg = number_format($avg,2);
        $percentage = ($avg/5)*(100);
        '<div class="star-ratings" style="width:84px"><div class="fill-ratings"  style="width:'.$percentage.'%" ><span class="neon-clr" style="width:84px">★★★★★</span></div><div class="empty-ratings"><span>★★★★★</span></div></div>';
        //  if($avg==0){ 
        //          $html.='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
        //     }else if($avg>=1 && $avg<=1.5){ 
        //       $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
        //     }else if($avg>=1.5 && $avg<=2){ 
        //       $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
        //     }else if($avg>=2 && $avg<=2.5){  
        //       $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
        //     }else if($avg>=2.5 && $avg<=3.0){  
        //       $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
        //     }else if($avg>=3.0 && $avg<=3.5){  
        //        $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
        //     }else if($avg>=3.5 && $avg<=4.0){ 
        //        $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
        //     }else if($avg>=4.0 && $avg<=4.5){  
        //       $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
        //     }else if($avg>=4.5 && $avg<=5.0){ 
        //       $html.='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> </div>'; 
        //     }else if($avg>=5.0 && $avg<=5.5){ 
        //        $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> </div>'; 
        //     }else if($avg>=5.0 && $avg<=5.5){ 
        //        $html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> </div>'; 
        //     }
             return $html;
      }
      function getAverage($sum,$number){
        return $number>0 ? number_format($sum/$number,2) : 0;
      }
      if (! function_exists('str_slug')) {
        function str_slug($string)
        {	
            return \Illuminate\Support\Str::slug($string);
        }
    }
    function generateRandomString($length) {
        return substr(str_shuffle(str_repeat($x='0123456789', ceil($length/strlen($x)) )),1,$length);
    }
    function getUserData($userId){
        return User::find($userId);
    }
