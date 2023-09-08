<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\{ProductCategory,ProductImage,Order,ReviewRating,ProductVideo};
use Auth, Str, Storage, DB;
use Carbon\Carbon;
use Image;
use Illuminate\Support\Facades\Log;
use File;
use Facebook\Facebook;
use App\Jobs\{FacebookPostPublish,InstagramPostPublish,ProcessVideo};

class Product extends Model
{
	use SoftDeletes;    
	public $timestamps = true;
	protected $dates = ['deleted_at'];
	protected $appends = ['type_name', 'delivery_name', 'display_path', 'display_thumb_path', 'prepared_tags', 'display_path_base64','video_path_url'];

	public function scopeMySelect($builder)
	{
	    $builder->addSelect(['id','title','price','quantity','slug', 'avg_rating', 'rating_count']);
	}
	
	/** Get Type Name **/
	public function getTypeNameAttribute()
	{
		if(!is_null($this->item_type) && $this->item_type > 0){
			$item_types = config('const.item_types');
			return $item_types[$this->item_type];
		}else{
			return '';
		}
	}

	/** Get Delivery Name **/
	public function getDeliveryNameAttribute()
	{
		if(!is_null($this->delivery_method) && $this->delivery_method > 0){
			$delivery_methods = config('const.delivery_methods');
			return $delivery_methods[$this->delivery_method];
		}
		return '';
	}

	/** Product Display Image **/
	public function getVideoPathUrlAttribute()
	{
		$image = $this->video_path;
		if($image){
			return url('storage/'.$image);
		}
		return '';
	}

	/** Product Display Image **/
	public function getDisplayPathAttribute()
	{
		$image = ProductImage::where('product_id', $this->id)->orderBy('id', 'asc')->first();
		if($image){
			return $image->url;
		}
		return '';
	}

	/** Product Display Thumbnail Image **/
	public function getDisplayThumbPathAttribute()
	{
		$image = ProductImage::where('product_id', $this->id)->orderBy('id', 'asc')->first();
		if($image){
			return $image->thumb_url;
		}
		return '';
	}

	public function getDisplayPathBase64Attribute()
	{
		$image = ProductImage::where('product_id', $this->id)->orderBy('id', 'asc')->first();
		if($image){
			$imageInfo = explode(".", $image->image_path); 
			$image = public_path('storage/'.$image->image_path);
			if(File::exists($image)){
				$image = file_get_contents($image);
				$imageType = end($imageInfo);
				return 'data:image/'.$imageType.';base64,'.base64_encode($image);
			}else{
				return '';
			}
		}
		return '';
		
	}
	
	/** Product Prepared Tags **/
	public function getPreparedTagsAttribute()
	{
		$tags = $this->tags;
		if(!is_null($tags)){
			$decoded_tags = json_decode($tags, true);
			$tags_string = '';
			$i = 1;
			foreach($decoded_tags as $key => $value){
				if($i < count($decoded_tags)){
					$tags_string .= ' #'.$value['value'].' ,';
				}else{
					$tags_string .= ' #'.$value['value'];
				}
				++$i;
			}
			return $tags_string;
		}
	}

	/*** Get Number Of Product Into The App **/
	public function getNumberOfProductInApp($request)
	{
		return $this->where('status', 1);
	}

	/*** Get Number Of Product For Chart to Single User grouping by months **/
	public function getNumberOfProductForChart($request){
		return $this->select(
						DB::raw('count(id) as value'), 
						DB::raw("DATE_FORMAT(created_at, '%Y-%m') date"),  
						DB::raw('YEAR(created_at) year, MONTH(created_at) month')
					)
					->where(['created_by' => Auth::id(),'status' => 1])
					->groupby('year','month')
					->get();
	}


	/*** Get the product videos for the product. ***/
    public function product_videos()
    {
        return $this->hasMany(ProductVideo::class);
    }


	/*** Get the product images for the product. ***/
    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /*** Get the orders for product. ***/
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
	
	/** Get ProductCategory for the product **/
    public function product_categories()
    {
    	 return $this->hasMany(ProductCategory::class);
    }
	
	public function review_ratings()
    {
        return $this->hasMany(ReviewRating::class, 'product_id', 'id')->get();
    }
	/** Get User for the product **/
    public function users()
    {
        return $this->belongsTo('App\Models\User','created_by');
    }

	/**Product Detail **/
	public function productDetail($request)
	{
		$prod = $this->where('slug', $request->slug)->withTrashed()->first();
		if(!empty($prod)){
			$prod->increment('views');
		}		
		$product = $this->select('*')->with([
				'users' => function($q){
					return $q->select('id','name','image_path','rating_count','avg_rating','paypal_email');
				},
				'product_images' => function($q) {
					return $q->select('id','image_path','image_type','name','thumb_path','product_id');
				},
				'product_videos' => function($q) {
					return $q->select('id','video_path','video_type','name','product_id');
				},
				'product_categories' => function ($query) use($prod) {
					return $query->with(['category' => function($catQuery){
						return $catQuery->select('id','name');
					}])->select('category_id','product_id')->where('product_id', $prod->id);
				}
		])->where('slug', $request->slug);
		return $product;
	}

	/**Similar Product Found Based On Category Ids**/
	public function similarItemsYouMayLike($conditions, $req)
	{
		$products = $this->select('id','title','price','created_by', 'slug')->with([
			'product_categories' => function ($query) use($conditions) {
        		return $query->whereIn('category_id', $conditions);
    		},
    		'users' => function($userQuery){
    			return $userQuery->select('id','name','image_path','rating_count','avg_rating');
    		}
    	])->where('status',1)->where('slug','!=' , $req->slug);
    	return $products;
	}
   
    public function createItem($request)
    {
		if($request->item_id == 0){
			$product = new Product();
			$product->created_by = Auth::id();
			$product->quantity = 1;
		}else{
			$product = Product::find($request->item_id);
		}  
		
        $product->title           = $request->input('title');
        $product->price           = $request->input('price');
        $product->description     = $request->input('description');
        
        $product->status          = $request->input('save_as_draft') == 'true' ? 0 : 1;
		if($request->item_id == 0){
			$product->slug            = Str::slug($request->input('title').'-'.rand(10,100));
		}
		if($request->delete_prod_video != '0'){
			Storage::disk('public')->deleteDirectory('products/'.$request->delete_prod_video.'/videos');
			$product->video_path = '';
		}
        $product->tags            = $request->input('tags') ? $request->input('tags'): NULL;
        $product->item_type       = $request->input('item_type');
        $product->delivery_time   = $request->input('delivery_time') ?? 1;
        $product->delivery_method = $request->input('delivery_method') ?? 1;
        $product->delivery_price  = $request->input('delivery_price') ?? 0.00;
		$product->address		  = $request->input('address');
		$product->lat 			  = $request->input('lat');
		$product->lng			  = $request->input('lng');
	    $product->created_by      = Auth::id();
		$product->updated_by      = Auth::id();
		
		$product->save();
		//save product category mapping
		if($request->filled('category_id')){
			$this->saveProductCategory($request, $product);
		}		
		
		//save image
		$this->saveProductImage($request, $product);

		// Call Save Video Function
		if ($request->hasFile('video_files')) {	
			$this->saveProductVideo($request, $product);
			// $folderPath = storage_path('app/public/products/'.$product->id.'/videos');
			// if(!File::isDirectory($folderPath)){
			// 	File::makeDirectory($folderPath, 0777, true, true);
			// }
			// ProcessVideo::dispatch($request, $product, $folderPath);
		}

		return true;
	}

	public function saveProductVideo($request,$product){
		$folderPath = storage_path('app/public/products/'.$product->id.'/videos');
		if(!File::isDirectory($folderPath)){
		    File::makeDirectory($folderPath, 0777, true, true);
		}
		$files = $request->file('video_files');
		Log::info('asx',['ss' => $files]);
		foreach($files as $key => $file){
			$extension  = $file->getClientOriginalExtension();
			$videoName = uniqid(). '.' .$extension;
			$path = $file->storeAs("products/".$product->id."/videos",$videoName,'public'); // Change 'public' to your disk name if necessary.
			$video = new ProductVideo();
			$video->product_id = $product->id;
			$video->video_type = $extension;
			$video->name       = $videoName;
			$video->size       = $file->getSize();
			$video->video_path = $path;
			$video->save();
		}
	}
	
	public function saveProductCategory($request, $product){
		//first delete all mapping
		ProductCategory::where('product_id', $product->id)->delete();
		$categories = $request->input('category_id');
		if(count($categories) > 0){
			foreach($categories as $category){
				$procat = new ProductCategory();
				$procat->product_id  = $product->id;
				$procat->category_id = $category;
				$procat->status      = 1;
				$procat->created_by  = Auth::id();
				$procat->updated_by  = Auth::id();
				$procat->save();
			}
		}
	}

	/*** Save Product Images ***/
	public function saveProductImage($request, $product){
		if($request->have_files == 'yes'){
			$files = $request->file('images');
			foreach($files as $key => $file){
				$filename   = $file->getClientOriginalName();
				$extension  = $file->getClientOriginalExtension();
				$image_name = date('mdYHis') . uniqid(). '.' .$extension;
				Storage::disk('public')->putFileAs('products/'.$product->id.'/', $file, $image_name);
				if($request->item_id == 0 && $key == 0){
					$productImg = url('storage/products/'.$product->id.'/'.$image_name);
					FacebookPostPublish::dispatch($product, $productImg);
					InstagramPostPublish::dispatch($product, $productImg);
				}
				
				// resize image to fixed size
				$file = Image::make($file);
				$file->resize(316,316);
				$thumbnailsFolderPath = storage_path('app/public/products/'.$product->id.'/thumbnails');
				if(!File::isDirectory($thumbnailsFolderPath)){
				    File::makeDirectory($thumbnailsFolderPath, 0777, true, true);
				}
				$file->save($thumbnailsFolderPath.'/'.$image_name, 80);

				$image = new ProductImage();
				$image->product_id = $product->id;
				$image->image_type = $extension;
				$image->name       = $image_name;
				$image->size       = Storage::disk('public')->size('products/'.$product->id.'/'.$image_name);
				$image->image_path = 'products/'.$product->id.'/'.$image_name;
				$image->thumb_path = 'products/'.$product->id.'/thumbnails/'.$image_name;
				$image->save();
			}

		}	

	}
	 
	public function getDataTotalRecords($request)
    { 
		$created_by = '';
	    if(Auth::user()->role == 2){ 
	    	$created_by=Auth::user()->id; 
	    }else if($request->filled('created_by')){
		   $created_by = decryptDataId($request->created_by);
	    }	 
		$startDate    = isset($request['start_date']) ? $request['start_date'] : '';
		$endDate      = isset($request['end_date']) ? $request['end_date'] : '';
	    $search       = isset($request['query']['search_string']) ? $request['query']['search_string'] : '';
	    $order_colomn = isset($request['sort']['field']) ? $request['sort']['field'] : 'id';
	    $order_type   = isset($request['sort']['sort']) ? $request['sort']['sort'] : 'desc';
	   
        $mainQuery = $this->with([
        				'product_categories' => function ($prodCatQuery) {
        					$prodCatQuery->with([
        						'category' =>  function ($catQuery) {
        							return $catQuery->select('id','name');
        						}
        					])->select('category_id','product_id');
        				},
						'users' => function($userQuery){
							return $userQuery->select('id','name');
						}
        			])
        			->when($search != '', function ($q) use ($search) {
                		$q->where(function ($query) use ($search) {
							$query->where('title',  'like',  '%' . $search . '%')
								->orWhere('description',  'like',  '%' . $search . '%');
						});
		            })
					->when($order_colomn != '', function ($query) use ($order_colomn, $order_type) {
						if($order_colomn != 'action' &&  $order_colomn != 'image'){
							return $query->orderBy($order_colomn, $order_type);
						}else{
							return $query->orderBy('id', 'desc');
						}
		            })
					->when(($startDate != '') && ($endDate != ''),function($query) use($startDate,$endDate){
						$query->whereBetween('created_at', [$startDate, date('Y-m-d', strtotime("+1 day", strtotime($endDate)))]);
					})
					->when( $created_by!='' , function ($query) use($created_by) {
				      $query->where('created_by', $created_by);
					})
					->where(['status' => 1])
		            ->select('title','id','quantity','price','slug','status','rating_count','avg_rating','created_by','created_at','updated_at');
					return $mainQuery;
    }

    /** For Search Product List Only **/
    public function getDataSearchRecords($request)
    {
       $search       = $request['query']['search_string'] ? $request['query']['search_string'] : '';
       $minPrice     = $request['query']['min_price'] ? $request['query']['min_price'] : '0';
       $categoryId   = $request['query']['category_id'] ? $request['query']['category_id'] : 0;
       $maxPrice     = $request['query']['max_price'];
       $sellerId     = isset($request['query']['seller_id']) ? $request['query']['seller_id'] : '';
	   $orderColumn  = $request['query']['sort_field'] ? $request['query']['sort_field'] : '';
	   $lat			 = isset($request['query']['lat']) ? $request['query']['lat'] : '';
	   $lng			 = isset($request['query']['lng']) ? $request['query']['lng'] : '';
	   $distanceInMiles	= isset($request['query']['distance_in_miles']) ? $request['query']['distance_in_miles'] : 0;
	   $orderType    = 'desc';

        $mainQuery = $this->when($search != '', function ($q) use ($search) {
		       		$q->where(function ($query)use ($search) {
						$query->where('title',  'like',  '%' . $search . '%')
							->orWhere('description',  'like',  '%' . $search . '%')
							->orWhereJsonContains('tags', ['value' =>  $search ]);
		            });
	            })
	       		->when($minPrice != '0' && $maxPrice != '0' && $minPrice != '' && $maxPrice != '', function ($q) use ($minPrice,$maxPrice) {
	       			$q->where(function ($query)use ($minPrice,$maxPrice) {
	       				$query->whereBetween('price', [$minPrice, $maxPrice]);
	       			});
	            })
	            ->with([
	            	'users' => function($q){
		            	return $q->select('id','name','image_path','rating_count','avg_rating');
		            },
		            'product_categories' =>  function ($query) use($categoryId) {
		        		return $query->with([
		        			'category' => function($catQuery){
		        				return $catQuery->select('id','name');
		        			}
		        		]);
	        		}
		        ])	
				->when($categoryId != 0, function ($q) use ($categoryId) {
					$q->whereHas('product_categories', function ($query) use($categoryId) {
						return $query->select('category_id','product_id')->where('category_id', $categoryId);
					});
				})
				->when($orderColumn != '', function ($query) use ($orderColumn, $orderType) {
					if($orderColumn == '2'){
						return $query->orderBy('price', $orderType);
					}else if($orderColumn == '3'){
						return $query->orderBy('price', 'asc');
					}else if($orderColumn == '4'){
						return $query->orderBy('views', $orderType);
					}else{
						return $query->orderBy('created_at', $orderType);
					}
	            })
	            ->when($sellerId != '' && $sellerId != '0', function($query) use($sellerId){
	            	return $query->where('created_by',$sellerId);
	            })
	            ->when(($lat != '') && ($lng != '') && ($distanceInMiles > 0), function($mainQuery) use($distanceInMiles,$lat,$lng){
					$distance_query = "(6371 * acos(cos(radians(" .$lat. ")) * cos(radians(`lat`)) * cos(radians(`lng`) - radians(" .$lng. ")) + sin(radians(" .$lat. ")) * sin(radians(`lat`))))";
					$mainQuery->selectRaw("{$distance_query} AS distance")->whereRaw("{$distance_query} < ?", $distanceInMiles);
	            });			
	            $mainQuery->where('status',1);

	            $mainQuery->select('products.*');
				return $mainQuery;
    }
	 
	public function deleteItem($id)
	{
		$product = Product::find($id);
		// $images = ProductImage::where('product_id', $id)->delete();
		// if(Storage::disk('public')->exists('products/'.$product->id)){
		// 	Storage::deleteDirectory('products/'.$product->id);
		// }
		$product->delete();
		return true;
	}
	
}