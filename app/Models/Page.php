<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\{PageContent,Faq,Feature};
use Auth, Str, Storage, DB;
use Carbon\Carbon;

class Page extends Model
{
    public $timestamps = true;
    
    public function pageContents(){
        return $this->hasMany(PageContent::class, 'page_id', 'id'); 
    }
    public static function boot() {
        parent::boot();

        static::deleting(function($page) { // before delete() method call this
             $page->pageContents()->delete();
             // do the rest of the cleanup...
        });
    }

    public function getDataTotalRecords($request)
    { 
		 
	    $search       = isset($request['query']['search_string']) ? $request['query']['search_string'] : '';
	    $order_colomn = isset($request['sort']['field']) ? $request['sort']['field'] : 'id';
	    $order_type   = isset($request['sort']['sort']) ? $request['sort']['sort'] : 'desc';
	   
        return $this->when($search != '', function ($q) use ($search) {
                		$q->where(function ($query)use ($search) {
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
		            ->select('pages.*');
    }
    public function createItem($request)
    {
		if($request->item_id == 0){
            $page = Page::where('title',$request->title)->first();
			if(!$page){
             $page = new Page();
            }
        }else{
			$page = Page::find($request->item_id);
		}  
		
        $page->title = $request->title;
        $page->slug = str_slug($request->title);
		if( $page->save() ){ 
            $banner_image_size='';
            $banner_image_name='';
            $banner_image_type=''; 
            $imageSizeArr = [];  $imageNameArr = [];  $imageTypeArr = [];  
          //save page content
         if($request->title=='About Us' || $request->title=='Faq' || $request->title=='Terms & Conditions' || $request->title=='Privacy Policy' || $request->title=='Contact Us' || $request->title=='Feature'){ 
            if($request->hasFile('about_us_banner_image') || $request->hasFile('faq_banner_image') || $request->hasFile('terms_banner_image') || $request->hasFile('privacy_banner_image') || $request->hasFile('contact_banner_image') || $request->hasFile('feature_banner_image')){
                $banner_image=$this->savePageBannerImage($request,$page);     
                if($request->item_id > 0){
                    $images = PageContent::where('page_id',$request->item_id)->where('param','banner_image');
                    //first delete old image
                   
                   foreach($images->get() as $image){	
                        $storagePath = Storage::disk('public')->path($image->value);
                        if(Storage::disk('public')->exists($image->value)){
                            Storage::disk('public')->deleteDirectory($image->value);
                            unlink($storagePath);
                        }
                        $image->delete();
                   }
                }
                $request->request->add(['banner_image'=>$banner_image['image_path']]);
                $banner_image_size=$banner_image['size'];
                $banner_image_name=$banner_image['name'];
                $banner_image_type=$banner_image['image_type'];
             } 
          }   
         if($request->title=='About Us'){    
             $images=$this->savePageImage($request,$page);
             $i = 1;
             foreach($images as $image){
              $indx = 'image_'.$i;
              $request->request->add([$indx=>$image['image_path']]);
              $imageSizeArr[$i]=$image['size'];
              $imageNameArr[$i]=$image['name'];
              $imageTypeArr[$i]=$image['image_type'];
              $i++;
             }
             
         } 
         if($request->title=='Faq' && $request->filled('question')){ 
            foreach($request->question as $key=>$question ){
                $saveFaqData['question'] = $question;
                $saveFaqData['answer'] = $request->answer[$key];
                Faq::create($saveFaqData);
            }
         }  
         
         //Feature start
            if($request->title=='Feature'){  
                if($request->item_id==0){
                   $images = []; 
                   for($i=1;$i<=5;$i++){
                       $des='description_'.$i;
                       $im='image_'.$i;
                         $image=$this->saveFeatureImage($request, $page,'images_'.$i);
                        if(!empty($image)){
                          array_push($images,$image);
                        }
                   }
                   $i = 1;
                   foreach($images as $image){
                     $indxImg = 'image_'.$i;
                     $indxDesc = 'description_'.$i;
                     $featureData['image'] = $image['image_path'];
                     $featureData['description'] = $request->$indxDesc;
                     Feature::create($featureData);           
                     $i++;
                    }

                } 
                $request->request->remove('description_1'); 
                $request->request->remove('description_2');
                $request->request->remove('description_3');
                $request->request->remove('description_4');
                $request->request->remove('description_5');
            }
         //Feature end
         
         $this->savePageContent($request,$page, $imageSizeArr,$imageNameArr,$imageTypeArr, $banner_image_size, $banner_image_name, $banner_image_type);
         return true;  
        }else{
            return false;
        }
	}
    public function savePageContent($request,$page, $imageSizeArr,$imageNameArr,$imageTypeArr, $banner_image_size, $banner_image_name, $banner_image_type){
       PageContent::where('page_id',$page->id)->where('param','!=','image_1')->where('param','!=','image_2')->where('param','!=','banner_image')->delete();
       foreach($request->except(['form_id','uploaded_path','have_files','title','item_id','about_us_banner_image','feature_banner_image','faq_banner_image','delete_path','image','question','answer','terms_banner_image','privacy_banner_image','contact_banner_image','images_1','images_2','images_3','images_4','images_5','feature_banner_image']) as $key=>$req){
          $data['page_id'] = $page->id;
          $data['param'] = $key;
          $data['value'] = $req;
          if($page->title=='About Us'){
                if($key=='image_1' || $key=='image_2'){
                    $arr=explode('_',$key);
                    $data['image_type'] = $imageTypeArr[$arr[1]];
                    $data['image_size'] = $imageSizeArr[$arr[1]];
                    $data['image_name'] = $imageNameArr[$arr[1]];
                    $count_image_1=PageContent::where('page_id',$page->id)->where('param','image_1')->count();
                    if($count_image_1>0){      
                        $data['param'] = 'image_2';
                    }
                    $count_image_2=PageContent::where('page_id',$page->id)->where('param','image_2')->count();
                    if($count_image_2>0){      
                        $data['param'] = 'image_1';
                    }
                }
          }
        
          if($key=='banner_image'){
            $arr=explode('_',$key);
            $data['image_type'] =  $banner_image_type;
            $data['image_size'] =  $banner_image_size;
            $data['image_name'] =  $banner_image_name;
          }
          PageContent::insert($data);
       } 
       return true;
    }
    public function savePageImage($request, $page)
	{
		$images = [];
        if($request->have_files == 'yes'){
			$files = $request->file('image');
			
            foreach($files as $file){
				$filename   = $file->getClientOriginalName();
				$extension  = $file->getClientOriginalExtension();
				$image_name = date('mdYHis') . uniqid(). '.' .$extension;
				Storage::disk('public')->putFileAs('pages/'.$page->id.'/', $file, $image_name);
				
				$image['page_id'] = $page->id;
				$image['image_type'] = $extension;
				$image['name']       = $image_name;
				$image['size']       = Storage::disk('public')->size('pages/'.$page->id.'/'.$image_name);
				$image['image_path'] = 'pages/'.$page->id.'/'.$image_name;
			    array_push($images,$image);
            }
		
        }
        return $images;	
	}
    public function savePageBannerImage($request, $page)
	{   
        if($request->hasFile('about_us_banner_image')) {
		  $file = $request->file('about_us_banner_image');
        }
        if($request->hasFile('faq_banner_image')) {
            $file = $request->file('faq_banner_image');
        }
        if($request->hasFile('feature_banner_image')) {
            $file = $request->file('feature_banner_image');
        }
        if($request->hasFile('terms_banner_image')) {
            $file = $request->file('terms_banner_image');
        }
        if($request->hasFile('privacy_banner_image')) {
            $file = $request->file('privacy_banner_image');
        }
        if($request->hasFile('contact_banner_image')) {
            $file = $request->file('contact_banner_image');
        }
		$filename   = $file->getClientOriginalName();
		$extension  = $file->getClientOriginalExtension();
		$image_name = date('mdYHis') . uniqid(). '.' .$extension;
		Storage::disk('public')->putFileAs('pages/'.$page->id.'/', $file, $image_name);
        
        $image['page_id'] = $page->id;
        $image['image_type'] = $extension;
        $image['name']       = $image_name;
        $image['size']       = Storage::disk('public')->size('pages/'.$page->id.'/'.$image_name);
        $image['image_path'] = 'pages/'.$page->id.'/'.$image_name;
        return $image;
    }
    public function saveFeatureImage($request, $page,$param)
	{ 
        $image = [];
       if($request->hasFile($param)) { 
		$file = $request->file($param);
     
		$filename   = $file->getClientOriginalName();
        $extension  = $file->getClientOriginalExtension();
        $image_name = date('mdYHis') . uniqid(). '.' .$extension;
        Storage::disk('public')->putFileAs('pages/'.$page->id.'/', $file, $image_name);
        
        $image['page_id'] = $page->id;
        $image['image_type'] = $extension;
        $image['name']       = $image_name;
        $image['size']       = Storage::disk('public')->size('pages/'.$page->id.'/'.$image_name);
        $image['image_path'] = 'pages/'.$page->id.'/'.$image_name;
      }	
        return $image;	
	}

}   


