<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Setting;
use Illuminate\Http\Request;
use Auth, Validator, Str, Gate;
use Illuminate\Validation\Rule;
use Session;
class SettingController extends Controller
{
	
	public $setting = '';
    public function __construct(Setting $setting){
        $this->setting = $setting;
    }
	
    public function index(Request $request)
    {   
    
        $settingsExist = Setting::whereIn('key', ['admin_commission_type','admin_commission_value','admin_email','tax'])->get();
       
        $settings = [];
        if (count($settingsExist) > 0) {
            
            foreach ($settingsExist as $key => $value) {
                if ($value->key == "admin_commission_type") {
                    $settings['admin_commission_type'] = $value->value;
                }
                if ($value->key == "admin_commission_value") {
                    $settings['admin_commission_value'] = $value->value;
                }
                if ($value->key == "admin_email") {
                    $settings['admin_email'] = $value->value;
                }
                if ($value->key == "tax") {
                    $settings['tax'] = $value->value;
                }
            }       
        }  
        if($request->isMethod('post')){
            $this->validate($request, [
                'admin_commission_value' => 'nullable|numeric|between:0,90','admin_email'=>'nullable|email','tax'=>'nullable|integer'
            ]);
           
           foreach($request->all() as $key=>$value){
             if($key!='_token' && $value!=''){
                $data=Setting::where('key',$key)->first();
                if($data){
                    $data->key = $key;
                    $data->value = $value;
                    $data->save();
                }else{
                    $data['key'] = $key;
                    $data['value'] = $value;
                    Setting::create($data); 
                } 
             }else{
                    Setting::where('key',$key)->delete();
             }  
           }
          Session::put('success','Success'); 
          return redirect()->back(); 
        } 
        
        return view('admin.setting.index',compact('settings'));
    }
}    