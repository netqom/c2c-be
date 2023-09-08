<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User,Product,Country,Page,Faq,Feature};
use Auth,Mail,DB;
use Illuminate\Support\Facades\{Lang,Hash,Validator}; 
use Carbon\Carbon; 
use Illuminate\Support\Str;

class PagesController extends Controller
{

    public $user;
    public $country;
    public $pageModel;
    public $faqModel;
    public $featureModel;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Feature $featureModel,Faq $faqModel,Page $pageModel,User $user, Country $country){
        $this->user         = $user;
        $this->country      = $country;
        $this->pageModel    = $pageModel;
        $this->faqModel     = $faqModel;
        $this->featureModel = $featureModel;
    }


    /** Send Conatct Us Form To Admin **/
    public function submitContactForm (Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);
        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $adminUser = User::where(['role' => '1'])->first();
        if(Mail::send('emails.contactUs', ['userData' => $request->all(), 'adminUser' => $adminUser ], function($message) use($adminUser){
            $message->to($adminUser->email);
            $message->subject('Contact-Us');
        })){
            return response()->json(['status' => true, 'message' => Lang::get('auth.contactUsSuccess')]);
        }
        return response()->json(['status' => true, 'message' => Lang::get('auth.passwordResetEmailSent')]);    
    }

    /*** All Pages Getting From The Same Function instead of faq ***/
    public function getPageContent(Request $request)
    {
        $pageContentList = '';
        $slug = $request->route()->getName();
        $pageData = $this->pageModel->with(['pageContents' => function ($pageContentsQue) {
            return $pageContentsQue->select('page_id','param','value');
        }])->select('title','slug','id')->where('slug',$slug)->first();
        if($slug == 'faq'){
            $faqQuery        = $this->faqModel->select('*');
            $pageContentList = $this->prepareData($faqQuery, $request); 
        }elseif($slug == 'feature'){
            $pageContentList = $this->featureModel->select('image','description')->get();
        }
        if(!empty($pageData)){
            return response()->json(['status' => true, 'message' => '', 'data' => $pageData, 'list' => $pageContentList ]);
        }
        return response()->json(['status' => true, 'message' => Lang::get('auth.passwordResetEmailSent') ,'data' => '' , 'list' => $pageContentList ]);
    }

}
