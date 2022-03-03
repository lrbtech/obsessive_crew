<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\city;
use App\shop_time;
use App\settings;
use App\app_settings;
use App\admin;
use App\membership;
use App\agent;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use DB;
use Mail;
use Hash;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        date_default_timezone_set("Asia/Dubai");
        date_default_timezone_get();
    }

    public function terms(){
        $settings = settings::first();
        return view('admin.terms',compact('settings'));
    }

    public function updateterms(Request $request){

        $settings = settings::find($request->id);
        $settings->terms_and_condition = $request->terms_and_condition;
        $settings->save();
        return back(); 
    }

    public function appterms(){
        $settings = app_settings::first();
        return view('admin.app_terms',compact('settings'));
    }

    public function updateappterms(Request $request){

        $settings = app_settings::find($request->id);
        $settings->terms_english = $request->terms_english;
        $settings->terms_arabic = $request->terms_arabic;
        $settings->save();
        return back(); 
    }

    public function appprivacy(){
        $settings = app_settings::first();
        return view('admin.app_privacy',compact('settings'));
    }

    public function updateappprivacy(Request $request){

        $settings = app_settings::find($request->id);
        $settings->privacy_english = $request->privacy_english;
        $settings->privacy_arabic = $request->privacy_arabic;
        $settings->save();
        return back(); 
    }

    public function appabout(){
        $settings = app_settings::first();
        return view('admin.app_about',compact('settings'));
    }

    public function updateappabout(Request $request){

        $settings = app_settings::find($request->id);
        $settings->about_english = $request->about_english;
        $settings->about_arabic = $request->about_arabic;
        $settings->save();
        return back(); 
    }

    public function changepassword()
    {
        $user = admin::find(Auth::guard('admin')->user()->id);
        return view('admin.changepassword',compact('user'));
    }

    public function updatepassword(Request $request){
        $request->validate([
            'oldpassword' => 'required',
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6'
        ]);
        
        $hashedPassword = Auth::guard('admin')->user()->password;
 
        if (\Hash::check($request->oldpassword , $hashedPassword )) {
 
            if (!\Hash::check($request->password , $hashedPassword)) {
 
                $admin = admin::find($request->id);
                $admin->password = Hash::make($request->password);
                $admin->save();
 
                return response()->json(['message' => 'Password Updated Successfully!' , 'status' => 1], 200);
            }
 
            else{
                return response()->json(['message' => 'new password can not be the old password!' , 'status' => 0]);
            }
 
           }
 
        else{
            return response()->json(['message' => 'old password doesnt matched!' , 'status' => 0]);
        }
    }

    public function membership(){
        $membership = membership::all();
        return view('admin.membership',compact('membership'));
    }

    public function savemembership(Request $request){
        $this->validate($request, [
            'name'=>'required',
            'from_value'=>'required',
            'to_value'=>'required',
            'discount'=>'required',
        ]);
        
        $membership = new membership;
        $membership->name = $request->name;
        $membership->from_value = $request->from_value;
        $membership->to_value = $request->to_value;
        $membership->discount = $request->discount;
        $membership->save();

        return response()->json('successfully save'); 
    }

    public function updatemembership(Request $request){
        $this->validate($request, [
            'name'=>'required',
            'from_value'=>'required',
            'to_value'=>'required',
            'discount'=>'required',
        ]);
        
        $membership = membership::find($request->id);
        $membership->name = $request->name;
        $membership->from_value = $request->from_value;
        $membership->to_value = $request->to_value;
        $membership->discount = $request->discount;
        $membership->save();

        return response()->json('successfully update'); 
    }

    public function editmembership($id){
        $membership = membership::find($id);
        return response()->json($membership); 
    }
    
    public function deletemembership($id,$status){
        $membership = membership::find($id);
        $membership->status = $status;
        $membership->save();
        return response()->json(['message'=>'Successfully Delete'],200); 
    }


    public function agent(){
        $agent = agent::all();
        return view('admin.agent',compact('agent'));
    }

    public function saveagent(Request $request){
        $this->validate($request, [
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
            // 'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            // 'password_confirmation' => 'min:6'
            'password' => 'required|min:6'
        ]);
        
        $agent = new agent;
        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->mobile = $request->mobile;
        $agent->password = Hash::make($request->password);
        $agent->save();

        return response()->json('successfully save'); 
    }

    public function updateagent(Request $request){
        $this->validate($request, [
            'name'=>'required',
            'email'=>'required',
            'mobile'=>'required',
            // 'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            // 'password_confirmation' => 'min:6'
        ]);
        
        $agent = agent::find($request->id);
        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->mobile = $request->mobile;
        if($request->password != ''){
        $agent->password = Hash::make($request->password);
        }
        $agent->save();

        return response()->json('successfully update'); 
    }

    public function editagent($id){
        $agent = agent::find($id);
        return response()->json($agent); 
    }
    
    public function deleteagent($id,$status){
        $agent = agent::find($id);
        $agent->status = $status;
        $agent->save();
        return response()->json(['message'=>'Successfully Delete'],200); 
    }


}
