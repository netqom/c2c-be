<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Auth, Storage, Str, DB;
use App\Models\{Product,Category,ProductCategory,ProductImage,ReviewRating,Chat,ProductVideo};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Lang,Validator};

class ProductsController extends Controller
{
		
    public $product;
	public $productCategory;
    public $category;
    public $productImageModel;
    public $productVideoModel;
    public $productReview;
    public $chat;

    public function __construct(ProductVideo $productVideoModel,Chat $chat,ProductImage $productImageModel ,Product $product, Category $category,ProductCategory $productCategory, ReviewRating $productReview){
        $this->product           = $product;
        $this->category          = $category;
        $this->productCategory   = $productCategory;
        $this->productImageModel = $productImageModel;
        $this->productVideoModel = $productVideoModel;
        $this->productReview     = $productReview;
        $this->chat     		 = $chat;
    }

    /** List Of Product Along With Pagination**/
    public function getProductList(Request $request)
	{
		$queryObj  = $this->product->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request);
		$data['draft_product'] = $this->product->select('id')->where(['created_by' => Auth::user()->id, 'status' => 0])->latest()->first();
		return response()->json($data);
	}

	/*** Get Draft Product */
	public function getDraftProduct(Request $request){
        $data = $this->product->select('id')->where(['created_by' => Auth::user()->id, 'status' => 0])->latest()->first();
        return response()->json(['status' => true, 'message' => '', 'data' => $data ]);
    }
    

	/** List Of Search Product **/
	public function searchProductList(Request $request){
		$queryObj  = $this->product->getDataSearchRecords($request);
		$data = $this->prepareData($queryObj, $request);
		return response()->json($data);
	}

	/** Get detail of any product using their id **/
	public function getProductDetail(Request $request){
		$productDetail = $this->product->productDetail($request)->first();
		
		if(!empty($productDetail)){
			$catArray = $productDetail->product_categories->map(function($item) { return $item->category_id; });
			$similarItemsYouMayLike = $this->product->similarItemsYouMayLike($catArray, $request)->inRandomOrder()->limit(4)->get();
			$estimateDeliveryTime = config('const.estimate_delivery_time');
			$hasOffer = $this->chat->where(['product_id' => $productDetail->id, 'chat_owner' => $request->user_id, 'has_offer'  => 1])->count();
			$reviews = $this->productReview->where('product_id', $productDetail->id)
							->with(['user' => function($userQuery){
								return $userQuery->select('id','name','image_path');
							}])->get();
			return response()->json(['status' => true, 'data' => $productDetail,'has_offer'=>$hasOffer, 'similar_items' => $similarItemsYouMayLike, 'reviews' => $reviews, 'estimate_delivery_time' => $estimateDeliveryTime, 'message' => '', 'error' => '',]);	
		}else{
			return response()->json(['status' => false, 'message' => "Unable to Find Product, might be deleted by User", 'error' => '', 'data' => $productDetail]);
		}
	}

	/** Add Edit For Of Product **/
	public function getProductFormData(Request $request)
	{
		$checkProd = $this->product->withTrashed()->find($request->id);
		
		if(isset($request->id) && $checkProd->created_by != Auth::user()->id){
			return response()->json(['status' => false,'message' => 'Unauthenticated', 'error' => 'Unauthenticated']);
		}
		$selectedCategories = [];
		if($request->id == 0){
			$data = new Product();
		}else{
			$data = $this->product->with(['product_videos','product_images'])->withTrashed()->find($request->id);
			$selectedCategories = $this->productCategory->with('category:id,name')->where('product_id', $request->id)->get();
		}
		$item_types = config('const.item_types');
		$estimateDeliveryTime = config('const.estimate_delivery_time');
		$hasConnectAccount = Auth::user()->connect_account != null && Auth::user()->connect_account->account_status == 'verified' ? true : false;
		
		$delivery_methods = config('const.delivery_methods');
		$categories = $this->category->select('id as value','name as label')->where(['status' => 1])->get();
        return response()->json(['status' => true, 'message' => '', 'error' => '', 'data' => $data,'categories' => $categories, 'item_types' => $item_types, 'delivery_methods' => $delivery_methods,'selectedCategories' => $selectedCategories,'hasConnectAccount' => $hasConnectAccount, 'estimate_delivery_time' => $estimateDeliveryTime ]);
	}

	/***Hard Delete On Clear Draft ***/
	public function clearDraftProduct(Request $request){
		$product = Product::find($request->product_id);
		if(!empty($product)){
			ProductImage::where('product_id', $request->product_id)->delete();
			if(Storage::disk('public')->exists('products/'.$product->id)){
				Storage::disk('public')->deleteDirectory('products/'.$product->id);
			}
			ProductCategory::where('product_id', $request->product_id)->delete();
			$product->forceDelete();
			return response()->json(['status' => true, 'message' => 'Draft cleared successfully']);
		}
		return response()->json(['status' => false, 'message' => 'Unable to Find Product, might be deleted by User']);
	}


	/** Save Product As Draft **/
	public function saveProductAsDraft(Request $request) {
		if($request->filled('category_id')){
			$request->merge(['category_id' => explode(',',$request->category_id)]);
		}		
		if($request->hasFile('images')){
        	$request->merge(['have_files' => "yes"]);
        }
        //return response()->json(['status' => false, 'message' => $request->all()]);
		$result = $this->product->createItem($request);
		if($result){
			return response()->json(['status' => true, 'message' => 'Product save as a draft successfully']);
		}else{
			return response()->json(['status' => false, 'message' => '']);
		}		
	}

	/** Save Product **/
	public function saveProduct(Request $request)
    { 
		
		$validator = Validator::make($request->all(), ['title' => 'required','description' => 'required']);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }    
		if($request->filled('category_id')){
			$request->merge(['category_id' => explode(',',$request->category_id)]);
		}
        if($request->hasFile('images')){
        	$request->merge(['have_files' => "yes"]);
        }
        //return response()->json(['status' => false, 'message' => $request->all()]);
		$result = $this->product->createItem($request);
		if($result){
			$type = $request->item_id == 0 ? 'Added' : 'Updated';
			return response()->json(['status' => true, 'message' => 'Product '.$type.' successfully', 'error' => '']);
		}else{
			return response()->json(['status' => false, 'message' => '']);
		}		
    }

    /** Delete Product Files **/
    public function deleteProductImage(Request $request)
	{
		$images = $this->productImageModel->deleteItem($request->item_id);
		if ($images) {
			$productImagesData = $this->productImageModel->where('product_id',$request->product_id)->get();
			return response()->json(['status' => true, 'message' => Lang::get('application.product.productImageRemove') , 'product_image' => $productImagesData]);
        } else {
			return response()->json(['status' => false, 'message' => Lang::get('auth.someError')]);
        }
	}

	/** Delete Product Video **/
    public function deleteProductVideo(Request $request)
	{
		$video = $this->productVideoModel->deleteItem($request->item_id);
		if ($video) {
			$productVideos = $this->productVideoModel->where('product_id',$request->product_id)->get();
			return response()->json(['status' => true, 'message' => Lang::get('application.product.productVideoRemove') , 'product_videos' => $productVideos]);
        } else {
			return response()->json(['status' => false, 'message' => Lang::get('auth.someError')]);
        }
	}

	/** Delete Product And Their Images **/
	public function deleteProduct(Request $request)
    {
		$result = $this->product->deleteItem($request->product_id);
        if ($result) {
			return response()->json(['status' => true, 'message' => Lang::get('application.product.productRemove')]);
        } else {
			return response()->json(['status' => false, 'message' => Lang::get('auth.someError') ]);
        }
    }

}