<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Category;
use Illuminate\Http\Request;
use Auth, Str, Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\{Lang,Validator}; 

class CatgoriesController extends Controller
{
	
	public $category = '';

    public function __construct(Category $category){
        $this->category = $category;
    }
	
	/** Get Sub Categories List**/
	public function getCategoryList(Request $request){
		$categories = $this->category->select('id as value','name as label')->where(['status' => 1, 'parent_id' => 0])->get();
		return response()->json(['status' => true, 'message' => Lang::get('application.categoryList'), 'categories' => $categories, 'error' => ''],200);
	}

}	