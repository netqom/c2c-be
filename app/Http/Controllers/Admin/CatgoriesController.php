<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Auth, Validator, Str, Gate;
use Illuminate\Validation\Rule;

class CatgoriesController extends Controller
{
	
	public $category = '';
    public function __construct(Category $category){
        $this->category = $category;
    }
	
    public function index()
    {
        return view('admin.category.list');
    }
	
	public function getData(Request $request)
	{
		$queryObj = $this->category->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request);
		return response()->json($data, 200);
	}

    public function getAddEditForm($id)
    {
		$title = '';
		if($id == 0){
			$title = 'Add Category';
			$data = new Category();
		}else{
			$title = 'Edit Category';
			$data = $this->category->find($id);
		}
        $categories = $this->category->where('status', 1)->get();
        $html = view('admin.category.add-edit', compact('data', 'id', 'categories'))->render();
		return response()->json(['type' => 'success', 'msg' => '', 'title' => $title, 'html' => $html]);
    }

    public function addUpdateCategory(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'name' => 'required',
		]);
        
        if ($validator->fails()) {
            return response()->json(['type' => 'error', 'msg' => 'Please check some data is missing']);
        }
		
		$result = $this->category->createItem($request);
		if($result){
			$type = $request->item_id == 0 ? 'Added' : 'Updated';
			return response()->json(['type' => 'success', 'msg' => 'Product category '.$type.' successfully']);
		}else{
			return response()->json(['type' => 'error', 'msg' => 'Some error occured please try again']);
		}
		
    }

    public function destroy($id)
    {
		$checkCategoryExist  = ProductCategory::where('category_id',$id)->first();
		if(empty($checkCategoryExist)){
			$result = $this->category->deleteItem($id);
			if ($result) {
				return response()->json(['type' => 'success', 'msg' => 'Product category removed successfully']);
			} else {
				return response()->json(['type' => 'error', 'msg' => 'Please try again. Some internal error occurred']);
			}
		}else{
			return response()->json(['type' => 'error', 'msg' => "Category contains products, can't delete."]);
		}
    }

	public function changeStatus(Request $request){
      	if($request->changeToStatus==0){   
	    	$count = ProductCategory::where('category_id',$request->id)->count();
			if($count == 0){
			 	Category::where('id',$request->id)->update(['status'=>$request->changeToStatus]);	
			 	return response()->json(['type' => 'success', 'msg' => 'Product category '.$request->changeToStatusText.' successfully']);
			} else {
			 	return response()->json(['type' => 'error', 'msg' => 'This category cannot be deactivated.Because there are some products belongs to this category.']);
			}
	   	}else{
		    Category::where('id',$request->id)->update(['status'=>$request->changeToStatus]);
			return response()->json(['type' => 'success', 'msg' => 'Product category '.$request->changeToStatusText.' successfully']);
	   	}
	}
}