<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator, Storage, Str;
use Illuminate\Support\Facades\{Lang}; 
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public $product;
    public $category;
    public $productcategory;
    public $productimage;
    public function __construct(Product $product, Category $category, ProductCategory $productcategory, ProductImage $productimage){
        $this->product         = $product;
        $this->category        = $category;
        $this->productcategory = $productcategory;
        $this->productimage    = $productimage;
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$front_app_url = env('FRONTEND_APP_URL');
        return view('admin.product.list')->with(['front_app_url' => $front_app_url]);
    }
	
	public function getData(Request $request)
	{ 
		$queryObj = $this->product->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request); 
		foreach($data['data'] as $q){ 
			if ($q instanceof \Illuminate\Database\Eloquent\Model) {
				collect($data['data'])->map(function($q) {
					$numberOfRating=$q->review_ratings()->count();
					$sumOfRating = $q->review_ratings()->sum('rating');
					$q['rating'] = $numberOfRating>0 ? number_format($sumOfRating/$numberOfRating,2) : 0;
                    return $q;
				});
			}
	    }
		return response()->json($data,200);
	}
	
	public function getAddEditForm($id)
	{
		$selectedCategories = [];
		if($id == 0){
			$data = new Product();
		}else{
			$data = $this->product->find($id);
			$selectedCategories = $this->productcategory->where('product_id', $id)->pluck('category_id')->toArray();
		}
		
		$item_types = config('const.item_types');
		$delivery_methods = config('const.delivery_methods');
		$categories = $this->category->all();
        return view('admin.product.add-edit', compact('data', 'id', 'categories', 'item_types', 'delivery_methods', 'selectedCategories'));
	}

    public function addUpdateProduct(Request $request)
    { 
        //echo"<pre>";print_r($request->all()); die;
		$validator = Validator::make($request->all(), [
			'title' => 'required',
			'description' => 'required',
		]);
        
        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator);
        }
		
		$result = $this->product->createItem($request);
		if($result){
			$type = $request->item_id == 0 ? 'Added' : 'Updated';
			return response()->json(['type' => 'success', 'msg' => 'Product '.$type.' successfully']);
		}else{
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError')]);
		}
    }
	
	public function getProductUploadedFiles(Request $request)
	{
		$images = $this->productimage->where('product_id', $request->item_id)->get();
		return response()->json(['type' => 'success', 'msg' => '', 'data' => $images]);
	}
	
	public function deleteProductFile(Request $request)
	{
		$images = $this->productimage->deleteItem($request->item_id);
		if ($images) {
			return response()->json(['type' => 'success', 'msg' => Lang::get('application.productImageRemove') ]);
        } else {
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError')]);
        }
	}

    public function destroy($id)
    {
		$result = $this->product->deleteItem($id);
        if ($result) {
			return response()->json(['type' => 'success', 'msg' => Lang::get('auth.productRemove')]);
        } else {
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError') ]);
        }
    }

	public function changeStatus(Request $request){
	    Product::where('id',$request->id)->update(['status'=>$request->changeToStatus]);	
		return response()->json(['type' => 'success', 'msg' => 'Product '.$request->changeToStatusText.' successfully']);
	}

	public function productDetail($slug,$tab=null)
	{ 
		$product = $this->product->with([
			'product_categories' => function ($query) {
				return $query->with([
					'category' => function($catQuery){
						return $catQuery->select('id','name');
					}
				])->select('category_id','product_id');
			}])->where(['slug' => $slug, 'status' => 1])->first();
		if(empty($product)){
			return redirect()->to('/admin/products')->with(['error' => 'This product does not exist anymore.']);
		}
		
		$catNameArray = $product->product_categories->map(function($item) { return $item->category->name; })->toArray();
		$selectedCategories = [];
		$selectedCategories = $this->productcategory->where('product_id', $product->id)->get();
		$item_types = config('const.item_types');
		$delivery_methods = config('const.delivery_methods');
		$id = $product->id;
		return view('admin.product.detail', compact('catNameArray','product', 'id','tab','selectedCategories','item_types','delivery_methods'));
	}
	
}