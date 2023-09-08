<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Page,PageContent,Faq,Feature};
use Illuminate\Http\Request;
use Validator, Auth,Lang;

class PagesController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
	protected $user;
	protected $product;
	protected $order;
    public $pagecontent;
    public $faq;
    public $feature;
    public function __construct(Page $page,PageContent $pagecontent,Faq $faq,Feature $feature)
    {
		$this->page    = $page;
        $this->pagecontent    = $pagecontent;
        $this->faq = $faq;
        $this->feature = $feature;
    }

    public function index(Request $request){
        return view('admin.pages.list');    
    }
    public function getData(Request $request)
	{  
		$queryObj = $this->page->getDataTotalRecords($request);
		$data = $this->prepareData($queryObj, $request);
		return response()->json($data,200);
	}
    public function getAddEditForm($id,$pag)
	{   
		$pages = config('const.pages');
		
        if($id == 0){
			$data = new Page();
		}else{
			$data = $this->page->find($id);
		}
        if($pag=='Home Page'){
         $view=view('admin.pages.add-edit', compact('data', 'id','pages','pag'));
        }else if($pag=='About Us'){
            $view=view('admin.pages.about-us', compact('data', 'id','pages','pag'));
        }else if($pag=='Faq'){
            $view=view('admin.pages.faq', compact('data', 'id','pages','pag'));
        }else if($pag=='Feature'){
            $view=view('admin.pages.feature', compact('data', 'id','pages','pag'));
        }else if($pag=='Terms & Conditions'){
            $view=view('admin.pages.terms-and-conditions', compact('data', 'id','pages','pag'));
        }else if($pag=='Privacy Policy'){
            $view=view('admin.pages.privacy-policy', compact('data', 'id','pages','pag'));
        }else if($pag=='Contact Us'){
            $view=view('admin.pages.contact-us', compact('data', 'id','pages','pag'));
        }else{
            return redirect()->back()->withErrors(['gfdfg'=>'Work in progress.']);
        }
        return $view; 
        
    }
    public function addUpdatePage(Request $request)
    { 
        //echo"<pre>";  print_r($request->all()); die;
		$validator = Validator::make($request->all(), [
			'title' => 'required',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withInput($request->input())->withErrors($validator);
        }
		
		$result = $this->page->createItem($request);
		if($result){
			$type = $request->item_id == 0 ? 'Added' : 'Updated';
			return response()->json(['type' => 'success', 'msg' => 'Page '.$type.' successfully']);
		}else{
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError')]);
		}
    }
    public function getConfigPages(Request $request){
        $title = "Select Page";
        $html = view('admin.pages.partials.config-pages')->render();
		return response()->json(['type' => 'success', 'msg' => '', 'title' => $title, 'html' => $html]);
    }
    public function checkPageExist(Request $request){
      
       if(Page::where('title',$request->page)->first()){
        return response()->json(['type' => 'error', 'msg' => 'This page is already exist.']);
       }else{
        return response()->json(['type' => 'success', 'msg' => 'Success']);
       }
    }
    public function deleteBannerImage(Request $request)
	{
		$images = $this->pagecontent->deleteBannerImages($request->id);
		if ($images) {
			return response()->json(['type' => 'success', 'msg' => Lang::get('application.productImageRemove') ]);
        } else {
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError')]);
        }
	} 
    public function getPagesUploadedFiles(Request $request){
       
        $data = $this->page->find($request->item_id);
        $images = []; 
        if($data->pageContents()->count()>0){ 
            foreach($data->pageContents()->get() as $cont){
               if($cont['param']=='image_1' || $cont['param']=='image_2'){
                $image['url'] = url('storage/'.$cont['value']);
                $image['image_path']=$cont['value'];
                $image['name']=$cont['image_name'];
                $image['size']=$cont['image_size'];
                $image['id']=$cont['id'];
                array_push($images,$image);
               }
            } 
        }
      
		return response()->json(['type' => 'success', 'msg' => '', 'data' => $images]); 
    }
    public function deletePageImage(Request $request)
	{
		$images = $this->pagecontent->deletePageImage($request->item_id);
		if ($images) {
			return response()->json(['type' => 'success', 'msg' => Lang::get('application.productImageRemove') ]);
        } else {
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError')]);
        }
	} 
    public function getFaqList(Request $request){
        $faqs = $this->faq->select('*');
        $data = $this->prepareData($faqs, $request); 
        return response()->json($data,200);
    }
    public function faqList(){
		return view('admin.pages.faq-list');
	}
    public function getFaqAddEditForm($id)
	{
		$title = '';
		if($id == 0){
			$data = new Faq();
			$title = 'Add Faq';
		}else{
			$data = $this->faq->find($id);
			//check if Admin
			if($data->role == 1){
				if($data->id != Auth::id()){
					return response()->json(['type' => 'error', 'msg' => "You don't have permission for this operation."]);	
				}
				$title = 'Update ';	
			}else{
				$title = 'Edit Faq';
			}
		}
	
		$html = view('admin.pages.faq-add-edit', compact('data'))->render();
		return response()->json(['type' => 'success', 'msg' => '', 'title' => $title, 'html' => $html]);
	}
    public function addUpdateFaq(Request $request)
	{
		$result = $this->faq->createUpdateItem($request);
		if($result){
			return response()->json(['type' => 'success', 'msg' => 'User Added/Updated successfully']);
		}else{
			return response()->json(['type' => 'success', 'msg' => 'Some error occured please try again']);
		}
	}
	public function deleteFaq(Request $request)
    {
		$result = $this->faq->destroy($request->id);
        if ($result) {
			return response()->json(['type' => 'success', 'msg' => 'Faq Removed.']);
        } else {
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError') ]);
        }
    } 
    public function deletePage(Request $request)
    { 
		$result = $this->page->destroy($request->id);
        if ($result) {
			return response()->json(['type' => 'success', 'msg' => 'Page Deleted.']);
        } else {
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError') ]);
        }
    } 
    public function featureList(){
		return view('admin.pages.feature-list');
	}
    public function getFeatureList(Request $request){
        $faqs = $this->feature->select('*');
        $data = $this->prepareData($faqs, $request); 
        return response()->json($data,200);
    }
    public function getFeaturesAddEditForm($id){
        $title = '';
		if($id == 0){
			$data = new Feature();
			$title = 'Add Feature';
            $featuresCount = Feature::count();
            if($featuresCount>=5){
                return response()->json(['type' => 'error', 'msg' => "Features limit reached."]);	  
            }
		}else{
			$data = $this->feature->find($id);
			//check if Admin
			if($data->role == 1){
				if($data->id != Auth::id()){
					return response()->json(['type' => 'error', 'msg' => "You don't have permission for this operation."]);	
				}
				$title = 'Update ';	
			}else{
				$title = 'Edit Feature';
			}
		}
	
		$html = view('admin.pages.feature-add-edit', compact('data'))->render();
		return response()->json(['type' => 'success', 'msg' => '', 'title' => $title, 'html' => $html]);   
    }
    public function addUpdateFeature(Request $request)
	{
		$result = $this->feature->createUpdateItem($request);
		if($result){
			return response()->json(['type' => 'success', 'msg' => 'Feature Added/Updated successfully']);
		}else{
			return response()->json(['type' => 'error', 'msg' => 'Some error occured please try again']);
		}
	}
    public function deleteFeature(Request $request)
    {
		$result = $this->feature->destroy($request->id);
        if ($result) {
			return response()->json(['type' => 'success', 'msg' => 'Feature Removed.']);
        } else {
			return response()->json(['type' => 'error', 'msg' => Lang::get('auth.someError') ]);
        }
    } 
}    