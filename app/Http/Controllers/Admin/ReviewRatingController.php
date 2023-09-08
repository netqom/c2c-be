<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator, Storage, Str;
use Illuminate\Support\Facades\{Lang};
use App\Models\ReviewRating; 
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ReviewRatingController extends Controller
{
	
	
    public $product;
    public $category;
    public $productcategory;
    public $productimage;
    public function __construct(ReviewRating $review_rating,Product $product, Category $category, ProductCategory $productcategory, ProductImage $productimage){
        $this->product         = $product;
        $this->category        = $category;
        $this->productcategory = $productcategory;
        $this->productimage    = $productimage;
        $this->review_rating    = $review_rating;
    }

    public function index(Request $request){ 
        $queryObj=$this->review_rating->getReviews($request);  
        $data = $this->prepareData($queryObj, $request);  
        foreach($data['data'] as $q){ 
        	if ($q instanceof \Illuminate\Database\Eloquent\Model) {
            collect($data['data'])->map(function($q) {
              $user=$q->user()->first();
                        $q['created_by_user_name']=$user->name;
              return $q;
            });
			    }
	      }
        return response()->json($data,200);
    }
    public function deleteReview(Request $request){
       $res = $this->review_rating->deleteItem($request->id);
       if ($res) {
         return response()->json(['type' => 'success', 'msg' => 'Review deleted successfully.' ]);
       } else {
         return response()->json(['type' => 'error', 'msg' => 'Something went wrong.']);
       }
    }
}    