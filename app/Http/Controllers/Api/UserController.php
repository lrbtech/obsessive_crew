<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\admin;
use App\customer;
use App\brand;
use App\vehicle_type;
use App\vehicles;
use App\vehicle_model;
use App\manage_address;
use App\city;
use App\service;
use App\booking;
use App\booking_service;
use App\temp_booking;
use App\temp_booking_service;
use App\shop_service;
use App\shop_package;
use App\shop_product;
use App\membership;
use App\shop_time;
use App\service_price;
use App\app_settings;
use App\colour;
use App\coupon;
use App\push_notification;
use App\reviews;
use App\admin_customer;
use App\bank_card;
use App\transaction_history;
use Hash;
use Auth;
use DB;
use Validator;
use Mail;
use PDF;
use Carbon\Carbon;
use App\Events\ChatEvent;
use App\Events\ChatAdmin;
use StdClass;
use Str;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\App;


class UserController extends Controller
{
    // private function send_sms($mobile,$msg)
    // {
    //   $requestParams = array(
    //     //'Unicode' => '0',
    //     //'route_id' => '2',
    //     'datetime' => '2020-09-27',
    //     'customername' => 'isalonuae',
    //     'password' => 'Ms5sbqBxif',
    //     'senderid' => 'ISalon UAE',
    //     'type' => 'text',
    //     'to' => '+91'.$mobile,
    //     'text' => $msg
    //   );
      
    //   //merge API url and parameters
    //   $apiUrl = 'https://smartsmsgateway.com/api/api_http.php?';
    //   foreach($requestParams as $key => $val){
    //       $apiUrl .= $key.'='.urlencode($val).'&';
    //   }
    //   $apiUrl = rtrim($apiUrl, "&");
    
    //   //API call
    //   $ch = curl_init();
    //   curl_setopt($ch, CURLOPT_URL, $apiUrl);
    //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    //   curl_exec($ch);
    //   curl_close($ch);
    // }

    private function send_sms($phone,$msg)
    {
        $requestParams = array(
          'api_key' => 'C2003249604f3c09173d94.20000197',
          'type' => 'text',
          'contacts' => '+971'.$phone,
          'senderid' => 'WellWellExp',
          'msg' => $msg
        );
        
        //merge API url and parameters
        $apiUrl = 'https://www.elitbuzz-me.com/sms/smsapi?';
        foreach($requestParams as $key => $val){
            $apiUrl .= $key.'='.urlencode($val).'&';
        }
        $apiUrl = rtrim($apiUrl, "&");
      
        //API call
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      
        curl_exec($ch);
        curl_close($ch);
    }

    public function sendwelcomemail($id){
        $customer = customer::find($id);
        Mail::send('mail.customer_welcome_mail',compact('customer'),function($message) use($customer){
            $message->to($customer['email'])->subject('Welcome Mail');
            //$message->from('mail.lrbinfotech@gmail.com','Obsessive Crew Website');
        });
    }

    public function createCustomer(Request $request){
        try{
            $email_exist = customer::where('email',$request->email)->get();
            if(count($email_exist)>0){
                return response()->json(['message' => 'This Email Address Has been Already Registered','status'=>403], 403);
            }
            $mobile_exist = customer::where('mobile',$request->mobile)->get();
            if(count($mobile_exist)>0){
                return response()->json(['message' => 'This Mobile Number Has been Already Registered','status'=>403], 403);
            }
            $randomid = mt_rand(1000,9999); 
            
            $customer = new customer;
            $customer->date = date('Y-m-d');
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->email = $request->email;
            $customer->mobile = $request->mobile;
            $customer->firebase_key = $request->firebase_key;
            $customer->city = $request->city;
            $customer->address = $request->address;

            // $uniqueid=uniqid($randomid, true);
            // $hashing = $request->mobile.$uniqueid;
            // $token_value=Hash::make($hashing);
            // $customer->token_value = $token_value;

            // if(isset($request->image)){
            //     if($request->file('image')!=""){
            //         $image = $request->image;
            //         $image_name = $request->image_name;
            //         $filename1='';
            //         foreach(explode('.', $image_name) as $info){
            //             $filename1 = $info;
            //         }
            //         $fileName = rand() . '.' . $filename1;
            //         $realImage = base64_decode($image);
            //         file_put_contents(public_path().'/profile_image/'.$fileName, $realImage);    
            //         $customer->image =  $fileName;
            //     }
            // }

            //$customer->otp = $randomid;
            //$customer->password = Hash::make($request->password);
            $customer->save();

            // $msg= "Dear Customer, Please use the code ".$customer->otp." to verify your Obsessive Crew Account";

            //$this->send_sms($customer->mobile,$msg);

            $this->sendwelcomemail($customer->id);

            return response()->json(
            ['message' => 'Register Successfully',
            'first_name'=>$customer->first_name,
            'last_name'=>$customer->last_name,
            'email'=>$customer->email,
            'mobile'=>$customer->mobile,
            //'token_value'=>$customer->token_value,
            'customer_id'=>$customer->id], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function updatefirebasekey(Request $request){
        try{
            $customer = customer::find($request->customer_id);
            $customer->firebase_key = $request->firebase_key;
            $customer->save();

            return response()->json(
            ['message' => 'Update Successfully',
            'customer_id'=>$customer->id],
             200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function updateCustomer(Request $request){
        try{
            $exist = customer::where('email',$request->email)->where('id','!=',$request->customer_id)->get();
            if(count($exist)>0){
                return response()->json(['message' => 'This Email Address Has been Already Registered','status'=>403], 403);
            }
            // $mobile_exist = customer::where('mobile',$request->mobile)->where('id','!=',$request->customer_id)->get();
            // if(count($mobile_exist)>0){
            //     return response()->json(['message' => 'This Mobile Number Has been Already Registered','status'=>403], 403);
            // }
            $randomid = mt_rand(1000,9999); 
            $mobile_status = 0;
            $customer = customer::find($request->customer_id);
            if(isset($request->first_name)){
                $customer->first_name = $request->first_name;
            }

            if(isset($request->last_name)){
                $customer->last_name = $request->last_name;
            }

            if(isset($request->email)){
                $customer->email = $request->email;
            }
            if(isset($request->city)){
                $customer->city = $request->city;
            }
            if(isset($request->address)){
                $customer->address = $request->address;
            }
            // $otp='';
            // if($request->mobile != $customer->mobile){
            //     $customer->mobile = $request->mobile;
            //     $customer->otp = $randomid;
            //     $otp = $randomid;
            //     $customer->status = 0;
            //     $msg= "Dear Customer, Please use the code ".$customer->otp." to verify your Obsessive Crew Account";
            //     $this->send_sms($customer->mobile,$msg);
            //     $mobile_status = 1;
            // }

            // if(isset($request->image)){
            //     if($request->file('image')!=""){
            //         $old_image = "profile_image/".$customer->image;
            //         if (file_exists($old_image)) {
            //             @unlink($old_image);
            //         }
            //         $image = $request->image;
            //         $image_name = $request->image_name;
            //         $filename1='';
            //         foreach(explode('.', $image_name) as $info){
            //             $filename1 = $info;
            //         }
            //         $fileName = rand() . '.' . $filename1;
            //         $realImage = base64_decode($image);
            //         file_put_contents(public_path().'/profile_image/'.$fileName, $realImage);    
            //         $customer->image =  $fileName;
            //     }
            // }

            // $uniqueid=uniqid($randomid, true);
            // $hashing = $request->mobile.$uniqueid;
            // $token_value=Hash::make($hashing);
            // $customer->token_value = $token_value;

            $customer->save();

            return response()->json(
            ['message' => 'Update Successfully',
            'name'=>$customer->first_name.' '.$customer->last_name,
            'email'=>$customer->email,
            //'token_value'=>$customer->token_value,
            //'mobile'=>$customer->mobile,
            //'otp'=>$otp,
            //'mobile_status'=>$mobile_status,
            'customer_id'=>$customer->id],
             200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function editCustomer($id){
        $customer = customer::find($id);
        $data = array(
            'mobile'=>$customer->mobile,
            'customer_id'=>$customer->id,
            'email'=>$customer->email,
            'city'=>$customer->city,
            'address'=>$customer->address,
            'first_name'=>$customer->first_name,
            'last_name'=> $customer->last_name,
        );
        return response()->json($data);
    }

    public function customerLogin(Request $request){
        $exist = customer::where('mobile',$request->mobile)->get();
        if(count($exist)>0){
            if($request->mobile == '564180384'){
                $randomid = 1234; 
            }
            else{
                $randomid = mt_rand(1000,9999); 
                $msg= "Dear Customer, Please use the code ".$randomid." to login your Obsessive Crew Account";
                $this->send_sms($request->mobile,$msg);
            }

            $customer = customer::find($exist[0]->id);
            $customer->firebase_key = $request->firebase_key;

            // $uniqueid=uniqid($randomid, true);
            // $hashing = $request->mobile.$uniqueid;
            // $token_value=Hash::make($hashing);
            // $customer->token_value = $token_value;

            $customer->save();

            return response()->json(
                ['message' => 'Login Successfully',
                'mobile'=>$request->mobile,
                'customer_id'=>$exist[0]->id,
                'email'=>$exist[0]->email,
                'city'=>$exist[0]->city,
                'address'=>$exist[0]->address,
                'first_name'=>$exist[0]->first_name,
                'last_name'=> $exist[0]->last_name,
                //'token_value'=> $exist[0]->token_value,
                'otp'=>$randomid,
                'status'=>1], 
            200);
        }else{
            if($request->mobile == '564180382'){
                $randomid = 1234; 
            }else{

                $randomid = mt_rand(1000,9999); 
            }

            // $uniqueid=uniqid($randomid, true);
            // $hashing = $request->mobile.$uniqueid;
            // $token_value=Hash::make($hashing);

            $msg= "Dear Customer, Please use the code ".$randomid." to register your Obsessive Crew Account";
            $this->send_sms($request->mobile,$msg);
            return response()->json(
                ['message' => 'New Login',
                'mobile'=>$request->mobile,
                'otp'=>$randomid,
                //'token_value'=> $token_value,
                'status'=>2], 
            200);
        }
    }

    public function forgetPassword(Request $request){
        try{
            $exist = customer::where('email',$request->email)->get();
            if(count($exist)>0){
                $customer = customer::find($exist[0]->id);
                $randomid = mt_rand(100000,999999);
                $customer->otp = $randomid;
                $customer->save();

                $msg= "Dear Customer, Please use the code ".$customer->otp." to Change your password";

                $this->send_sms($customer->mobile,$msg);        

                return response()->json(['message' => 'Successfully Send','customer_id'=>$customer->id], 200);
            }else{
                return response()->json(['message' => 'this Email Address Not Registered','status'=>403], 403);
            }
        
        }catch (\Exception $e) {
            return response()->json(['message' => 'this Email Address Not Registered','status'=>200], 200);
        }
    }

    public function resetPassword(Request $request)
    {
        if($request->customer_id !=null){
            $customer = customer::find($request->customer_id);
            if($customer->otp == $request->otp){
                $customer->password = Hash::make($request->get('password'));
                $customer->save();
                return response()->json(['message' => 'Successfully Reset'], 200);
            }else{
                return response()->json(['message' => 'Verification Code Not Valid','status'=>400], 400);
            }
        }else{
            return response()->json(['message' => 'Customer id not found'], 400);
        }
    }

    public function changePassword(Request $request){
        $customer = customer::find($request->customer_id);
        $hashedPassword = $customer->password;
 
        if (\Hash::check($request->oldpassword , $hashedPassword )) {
            if (!\Hash::check($request->password , $hashedPassword)) {
                $customer->password = Hash::make($request->password);
                $customer->save();
                return response()->json(['message' => 'Successfully Update'], 200);
            }
            else{
                return response()->json(['message' => 'new password can not be the old password!','status'=>400], 400);
            }
        }
        else{
            return response()->json(['message' => 'old password doesnt matched','status'=>400], 400);
        }
    }

    public function getApiOtpResend(Request $request)
    {
        if($request->customer_id !=null){
            $customer = customer::find($request->customer_id);
            $randomid = mt_rand(1000,9999);
            $customer->otp = $randomid;
            $customer->save();
            $msg= "Dear Customer, Please use the code ".$customer->otp." to verify your Obsessive Crew Account";
            $this->send_sms($customer->mobile,$msg);
            return response()->json(['message' => 'Otp Send Successfully'], 200);
        }else{
            return response()->json(['message' => 'Customer id not found'], 400);
        }
    }

    public function verifyCustomer(Request $request)
    {
        if($request->customer_id !=null){
            $customer = customer::find($request->customer_id);
            if($customer->otp == $request->otp){
                $customer->status = 1;
                $customer->save();
                return response()->json(['message' => 'Verified Your Account','name'=>$customer->first_name.' '.$customer->last_name,
                'email'=>$customer->email,
                'mobile'=>$customer->mobile,
                'customer_id'=>$customer->id,
                'status'=>200], 200);
            }else{
                return response()->json(['message' => 'Verification Code Not Valid','status'=>400], 400);
            }
        }else{
            return response()->json(['message' => 'Customer id not found'], 400);
        }
    }

    public function getcity(){
        $data = city::where('status',0)->get();
        foreach ($data as $key => $value) {
            // $data = array(
            //     'city' => $value->city,
            // );
            $datas[] = $value->city;
        }   
        return response()->json($datas); 
    }

    public function getbrand(){
        $data = brand::where('status',0)->get();
        $datas=array();
        foreach ($data as $key => $value) {
            // $data = array(
            //     'brand_name' => $value->brand_name,
            //     'image' => $value->image,
            // );
            $datas[] = $value->brand_name;
        }   
        return response()->json($datas); 
    }

    public function getvehiclemodel($brand){
        $brand = brand::where('brand_name',$brand)->first();
        $data = vehicle_model::where('brand_id',$brand->id)->get();
        $datas=array();
        foreach ($data as $key => $value) {
            $datas[] = $value->model_name;
        }   
        return response()->json($datas); 
    }
    public function getvehicletype(){
        $data = vehicle_type::where('status',0)->get();
        foreach ($data as $key => $value) {
            $data = array(
                'vehicle_type' => $value->vehicle_type,
                'image' => $value->image,
            );
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function getcolours(){
        $data = colour::where('status',0)->get();
        foreach ($data as $key => $value) {
            $data = array(
                '_id' => $value->id,
                'name' => $value->name,
                'code' => $value->code,
            );
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }


    public function savevehicles(Request $request){
        try{            
            $vehicles = new vehicles;
            $vehicles->date = date('Y-m-d');
            $vehicles->user_id = $request->user_id;
            $vehicles->vehicle_name = $request->vehicle_name;
            $vehicles->brand = $request->brand;
            //$vehicles->vehicle_type = $request->vehicle_type;
            $vehicles->colour = $request->colour;
            $vehicles->registration_city = $request->registration_city;
            $vehicles->registration_code = $request->registration_code;
            $vehicles->registration_number = $request->registration_number;
            $vehicles->save();

            return response()->json(
            [
            'message' => 'Register Successfully',
            'vehicle_id'=>$vehicles->id,
            ], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function editvehicles($id){
        $data = vehicles::where('id',$id)->get();
        foreach ($data as $key => $value) {
            $colour = colour::find($value->colour);
            $data = array(
                'vehicle_id' => $value->id,
                'vehicle_name' => $value->vehicle_name,
                'brand' => $value->brand,
                'colour_name' => $colour->name,
                'colour_code' => $colour->code,
                'colour_id' =>(int)$value->colour,
                'registration_city' => $value->registration_city,
                'registration_code' => $value->registration_code,
                'registration_number' => $value->registration_number,
            );
            $datas[] = $data;
        }   
        return response()->json($data); 
    }

    public function updatevehicles(Request $request){
        try{            
            $vehicles = vehicles::find($request->id);
            //$vehicles->user_id = $request->user_id;
            $vehicles->vehicle_name = $request->vehicle_name;
            $vehicles->brand = $request->brand;
            //$vehicles->vehicle_type = $request->vehicle_type;
            $vehicles->colour = $request->colour;
            $vehicles->registration_city = $request->registration_city;
            $vehicles->registration_code = $request->registration_code;
            $vehicles->registration_number = $request->registration_number;
            $vehicles->save();

            return response()->json(
            [
            'message' => 'Update Successfully',
            'vehicle_id'=>$vehicles->id,
            ], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function deletevehicles($id){
        try{            
            $vehicles = vehicles::find($id);
            $vehicles->status = 1;
            $vehicles->save();

            return response()->json(
            [
            'message' => 'Delete Successfully',
            'vehicle_id'=>$vehicles->id,
            ], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function getvehicles($id){
        $data = vehicles::where('user_id',$id)->where('status',0)->get();
        $datas=array();
        foreach ($data as $key => $value) {
            // $brand = brand::find($value->brand);
            $colour = colour::find($value->colour);
           // $vehicle_type = vehicle_type::find($value->vehicle_type);
            $data = array(
                'vehicle_id' => $value->id,
                'vehicle_name' => $value->vehicle_name,
                'brand' => $value->brand,
                // 'brand_image' => $brand->image,
                // 'vehicle_type' => $vehicle_type->vehicle_type,
                // 'vehicle_type_image' => $vehicle_type->image,
                'colour' => $colour->code,
                // 'registration_city' => $value->registration_city,
                'registration_code' => $value->registration_code,
                'registration_number' => $value->registration_number,
            );
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }


    public function savecard(Request $request){
        try{            
            $card = new bank_card;
            $card->user_id = $request->user_id;
            $card->card_name = $request->card_name;
            $card->card_no = $request->card_no;
            //$card->expiry_month = $request->expiry_month;
            //$card->expiry_year = $request->expiry_year;
            $card->cvv_no = $request->cvv_no;
            $card->token_value = $request->token_value;
            $card->card_info = $request->card_info;
            $card->save();

            return response()->json(
            [
            'message' => 'Register Successfully',
            'card_id'=>$card->id,
            ], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function editcard($id){
        $data = bank_card::where('id',$id)->get();
        foreach ($data as $key => $value) {
            $data = array(
                'card_id' => $value->id,
                'card_name' => $value->card_name,
                'card_no' => $value->card_no,
                // 'expiry_month' => $value->expiry_month,
                // 'expiry_year' => $value->expiry_year,
                'cvv_no' => $value->cvv_no,
                'token_value' => $value->token_value,
                'card_info' => $value->card_info,
            );
            $datas[] = $data;
        }   
        return response()->json($data); 
    }

    public function updatecard(Request $request){
        try{            
            $card = bank_card::find($request->id);
            $card->card_name = $request->card_name;
            $card->card_no = $request->card_no;
            //$card->expiry_month = $request->expiry_month;
            //$card->expiry_year = $request->expiry_year;
            $card->cvv_no = $request->cvv_no;
            $card->token_value = $request->token_value;
            $card->card_info = $request->card_info;
            $card->save();

            return response()->json(
            [
            'message' => 'Update Successfully',
            'card_id'=>$card->id,
            ], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function deletecard($id){
        try{            
            $card = bank_card::find($id);
            //$card->status = 1;
            $card->delete();

            return response()->json(
            [
            'message' => 'Delete Successfully',
            //'card_id'=>$card->id,
            ], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function getcard($id){
        $data = bank_card::where('user_id',$id)->where('status',0)->get();

        $getheaders = apache_request_headers();
        $token = $getheaders['APP_KEY'];
        //$customer = customer::find($id);

        //Lrbinfotech@2022
        if($token != '$2y$10$FL1.aFTP/tZ1zhEBdHMy..YrUzI2viLVGwDbUDGkHn4XR2HJZ3It6'){
            return response()->json('App Key Not Found', 401);
        }
        else{
            $datas=array();
            foreach ($data as $key => $value) {
                $data = array(
                    'card_id' => $value->id,
                    'card_name' => $value->card_name,
                    'card_no' => $value->card_no,
                    'token_value' => $value->token_value,
                    'cvv_no' => $value->cvv_no,
                    //'expiry_month' => $value->expiry_month,
                    //'expiry_year' => $value->expiry_year,
                    'card_info' => '',
                );
                if($value->card_info != null){
                    $data['card_info'] = $value->card_info;
                }
                $datas[] = $data;
            }   
            return response()->json($datas); 
        }
    }

    public function getnotification($id){
        $data = push_notification::where('status',1)->where('send_to',2)->get();
        $data1 = push_notification::where('status',1)->where('send_to',4)->get();
        foreach ($data as $key => $value) {
            $data = array(
                'title' => $value->title,
                'description' => '',
            );
            if($value->description != null){
                $data['description'] = $value->description;
            }
            $datas[] = $data;
        }   
        
        foreach ($data1 as $key => $value) {
            $arraydata=array();
            foreach(explode(',',$value->customer_ids) as $customer1){
                $arraydata[]=$customer1;
            }
            if(in_array($id , $arraydata))
            {
                $data = array(
                    'title' => $value->title,
                    'description' => '',
                );
                if($value->description != null){
                    $data['description'] = $value->description;
                }
                $datas[] = $data;
            }
        }   
        return response()->json($datas); 
    }

    public function saveaddress(Request $request){
        try{
            $ma = new manage_address;
            $ma->map_title = $request->map_title;
            $ma->addr_title = $request->addr_title;
            $ma->address = $request->address;
            $ma->landmark = $request->landmark;
            $ma->lat = $request->lat;
            $ma->lng = $request->lng;
            $ma->city = $request->city;
            $ma->customer_id = $request->customer_id;
            $ma->status =0;
            $ma->save();
            return response()->json(['message' => 'Address Store Successfully','id'=>$ma->id], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => ' Server Busy','status'=>400], 400);
        }
    }
     
    public function getaddress($id){
        $addr = manage_address::where('customer_id',$id)->select('map_title','lat','lng','addr_title','address','id','city','landmark')->where('status',0)->get();
        return response()->json($addr);
    }

    public function showaddress($id){
        $addr = manage_address::where('id',$id)->select('map_title','lat','lng','addr_title','address','id','city','landmark')->where('status',0)->first();
        return response()->json($addr);
    }

    public function updateaddress(Request $request){
        try{
            $ma =  manage_address::find($request->addr_id);
            $ma->map_title = $request->map_title;
            $ma->addr_title = $request->addr_title;
            $ma->address = $request->address;
            $ma->landmark = $request->landmark;
            $ma->lat = $request->lat;
            $ma->lng = $request->lng;
            $ma->city = $request->city;
            $ma->save();
            return response()->json(['message' => 'Address Update Successfully',], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => ' Server Busy','status'=>400], 400);
        }
    }

    public function deleteaddress($id){
        $address = manage_address::find($id);
        $address->status = 1;
        $address->save();
        return response()->json(['message' => 'Address Delete Successfully',], 200);
    }

    public function getterms($lang){
        if($lang == "en"){
            $data = app_settings::select('terms_english')->first();
            return response()->json($data->terms_english);
        }
        else{
            $data = app_settings::select('terms_arabic')->first();
            return response()->json($data->terms_arabic);
        }
    }

    public function getprivacy($lang){
        if($lang == "en"){
            $data = app_settings::select('privacy_english')->first();
            return response()->json($data->privacy_english);
        }
        else{
            $data = app_settings::select('privacy_arabic')->first();
            return response()->json($data->privacy_arabic);
        }
    }
    public function getabout($lang){
        if($lang == "en"){
            $data = app_settings::select('about_english')->first();
            return response()->json($data->about_english);
        }
        else{
            $data = app_settings::select('about_arabic')->first();
            return response()->json($data->about_arabic);
        }
    }


    public function getcategory($id){
        $data = service::where('type',$id)->where('parent_id',0)->where('status',0)->get();
        foreach ($data as $key => $value) {
            $data = array(
                'service_name_english' => $value->service_name_english,
                'service_name_arabic' => $value->service_name_arabic,
                'image' => (string)$value->image,
                'approx' => '50',
                'id' => $value->id,
            );
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function getshopreviews($id){
        $data = reviews::where('shop_id',$id)->where('status',1)->get();
        foreach ($data as $key => $value) {
            $customer = customer::find($value->customer_id);
            //$vehicle_type = vehicle_type::find($value->vehicle_type);
            $data = array(
                'date' => $value->date,
                'reviews' => (int)$value->reviews,
                'comments' => $value->comments,
                'customer_name' => $customer->first_name.' '.$customer->last_name,
            );
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function getmembership($customer_id){
        $cfdate = date('Y-m-d',strtotime('first day of this month'));
        $cldate = date('Y-m-d',strtotime('last day of this month'));
        $booking_value = booking::whereBetween('date', [$cfdate, $cldate])->where('customer_id',$customer_id)->get()->sum("total");

        $membership = membership::where('status',0)->get();
        $membership_discount=0;
        $membership_name='';
        if(!empty($membership)){
            foreach($membership as $row){
                if($row->from_value <= $booking_value && $row->to_value >= $booking_value ){
                    $membership_discount = $row->discount;
                    $membership_name = $row->name;
                }
            }
        }
        else{
            $membership_discount=0;
            $membership_name='';
        }

        $data = array(
            'membership_name' => $membership_name,
            'membership_discount' => (double)$membership_discount,
            'booking_value' => $booking_value,
        );
        return response()->json($data); 
    }

    public function getallshops($latitude,$longitude,$service,$getdate,$gettime,$type){
        $user = DB::table("users as u")
        ->select("u.*"
        ,DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
        * cos(radians(u.latitude)) 
        * cos(radians(u.longitude) - radians(" . $longitude . ")) 
        + sin(radians(" .$latitude. ")) 
        * sin(radians(u.latitude))) AS distance"))
        ->orderBy('distance', 'ASC')
        ->where("u.other_service",$type)
        ->where("u.role_id",'admin')
        //->whereIn('u.service_ids',$service)
        ->where('u.status',0)
        //->groupBy("u.id")
        // ->inRandomOrder()->limit(10)->get();
        ->get();
    
        $data =array();
        $datas =array();
        $check = 0;
        $check_date = 0;
        foreach ($user as $key => $value) {

foreach(explode(',',$value->service_ids) as $service_id){
    if($service == '0'){
        $check = 1;
        break;
    }
    elseif($service_id == $service){
        $check = 1;
        break;
    }
    else{
        $check = 0;
    }
}
if($check == 1){
    $getdate1 = date("l" , strtotime($getdate));
    $shop_time = shop_time::where('agent_id',$value->id)->where('days',$getdate1)->where('status',1)->first();
    date_default_timezone_set("Asia/Dubai");
    date_default_timezone_get();
    $today = date("l");
    $time = date("h:i A"); 
    if(!empty($shop_time)){
        if( strtotime($shop_time->open_time) <= strtotime($gettime) && strtotime($gettime) <= strtotime($shop_time->close_time) ){
            $check_date = 1;
        }
        else{
            $check_date = 0;
        }
    }
    else{
        $check_date = 0;
    }
    if($check_date == 1){
        $distance=0;
        if(round($value->distance,3) > 0.999 ){
            $distance = round($value->distance,3) . ' km';
        }
        else{
            $distance = substr($value->distance,-3) . ' m';
        }
        
        $data = array(
            'review_count' => '',
            'review_average' => '',
            'shop_id' => (int)$value->id,
            'cover_image' => '',
            'profile_image' => '',
            'address' => '',
            'shop_name' => $value->busisness_name,
            'latitude' => $value->latitude,
            'longitude' => $value->longitude,
            'towing_service' => 0,
            'about_us_english' => '',
            'about_us_arabic' => '',
            'mobile' => $value->mobile,
            'distance' => $distance,
            'average_cost' => $value->min_order_value,
        );
        $towing_count=0;
        $towing_service=0;
        // foreach(explode(',',$value->service_ids) as $service){
        //     if($service == '1'){
        //         $towing_service = 1;
        //     }
        //     $towing_count++;
        // }
        // if($towing_count > 1){
        //     $data['towing_service'] = (int)$towing_service;
        // }
        // else{
        //     $data['towing_service'] = 2;
        // }
        if($value->other_service == 2){
            $data['towing_service'] = 1;
        }
        if($value->about_us_english != null){
            $data['about_us_english'] = $value->about_us_english;
        }
        if($value->about_us_arabic != null){
            $data['about_us_arabic'] = $value->about_us_arabic;
        }
        if($value->address != null ){
            $data['address'] = $value->address;
        }
        if($value->cover_image != null){
            $data['cover_image'] = $value->cover_image;
        }
        if($value->profile_image != null){
            $data['profile_image'] = $value->profile_image;
        }
        $q =DB::table('reviews as r');
        $q->where('r.shop_id', '=', $value->id);
        $q->where('r.status', '=', 1);
        $q->groupBy('r.shop_id');
        $q->select([DB::raw("(count(*)) AS review_count"), DB::raw("(sum(r.reviews) / count(*)) AS review_average")]);
        $review = $q->first();

        if(!empty($review)){
            $data['review_count'] = (string)$review->review_count;
            $data['review_average'] = (string)$review->review_average;
        }
        $datas[] = $data;
    }
}

        }   
        if(count($datas)>0){

            return response()->json($datas); 
        }else{
            return response()->json($datas=[]); 

        }
    }


    public function getservice(){
        $service = service::where('parent_id',0)->where('status',0)->get();
        $data =array();
        $datas =array();
        foreach ($service as $key => $value) {
            $data = array(
                'service_id' => (int)$value->id,
                //'parent_id' => (int)$value->parent_id,
                'service_image' => $value->image,
                'service_name_english' => $value->service_name_english,
                'service_name_arabic' => '',
                //'price' => (string)$value->price,
            );
            if($value->service_name_arabic != null){
                $data['service_name_arabic'] = $value->service_name_arabic;
            }
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function getsubservice(){
        $service = service::where('parent_id','!=',0)->where('status',0)->get();
        $data =array();
        $datas =array();
        foreach ($service as $key => $value) {
            $data = array(
                'service_id' => (int)$value->id,
                'parent_id' => (int)$value->parent_id,
                'service_image' => $value->image,
                'service_name_english' => $value->service_name_english,
                'service_name_arabic' => '',
                'price' => (double)$value->price,
            );
            if($value->service_name_arabic != null){
                $data['service_name_arabic'] = $value->service_name_arabic;
            }
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function getproduct($id){
        $product = shop_product::where('shop_id',$id)->where('admin_status',1)->where('status',0)->get();
        $data =array();
        $datas =array();
        foreach ($product as $key => $value) {
            $data = array(
                'product_id' => (int)$value->id,
                'product_image' => $value->image,
                'product_name_english' => $value->product_name_english,
                'product_name_arabic' => '',
                'price' => (double)$value->price,
            );
            if($value->product_name_arabic != null){
                $data['product_name_arabic'] = $value->product_name_arabic;
            }
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function getpackage($id){
        $package = shop_package::where('shop_id',$id)->where('status',0)->get();
        $data =array();
        $datas =array();
        foreach ($package as $key => $value) {
            $data = array(
                'package_id' => (int)$value->id,
                'package_image' => $value->image,
                'package_name_english' => $value->package_name_english,
                'package_name_arabic' => '',
                'price' => (double)$value->price,
            );
            if($value->package_name_arabic != null){
                $data['package_name_arabic'] = $value->package_name_arabic;
            }
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function getpackageservices($id){
        $package = shop_package::find($id);
        $data =array();
        $datas =array();
        foreach(explode(',',$package->service_ids) as $service_id){
            $service = service::find($service_id);
            $data = array(
                'service_id' => (int)$service->id,
                'service_name' => $service->service_name_english,
                'service_image' => $service->image,
            );
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }


public function couponapply($id,$code,$value){
$coupon = coupon::where('coupon_code',$code)->where('status',1)->get();
if(count($coupon)>0){
    // if($shop_id == $coupon[0]->shop_id || $coupon[0]->shop_id == 'admin'){
        if($value >= $coupon[0]->minimum_order_value){
            if($coupon[0]->start_date <= date('Y-m-d') && $coupon[0]->end_date >= date('Y-m-d')){
                // return response()->json(['message' => 'Valid Date',], 200);
                if($coupon[0]->user_type ==1){
                    $arraydata=0;
                    foreach(explode(',',$coupon[0]->user_id) as $user1){
                        if($id == $user1){
                            $arraydata=1;
                        }
                    }
                    if($arraydata==0){
                        return response()->json(['message' => 'coupon not valid for you',], 400);
                    }else{
                        if($coupon[0]->limit_per_user !=null){
                        $booking_count = booking::where('customer_id',$id)->where('coupon_id',$coupon[0]->id)->get();
                        if(count($booking_count)< $coupon[0]->limit_per_user){
                            $amount = 0;
                            if($coupon[0]->discount_type == 4){
                            $amount = ($value/100) * $coupon[0]->amount;
                            }
                            else{
                                $amount = $coupon[0]->amount;
                            }
                            return response()->json([
                                'message' => 'Coupon Updated Succssfully',
                                'coupon_id' => $coupon[0]->id,
                                'discount'=>$amount
                            ], 200);
                        }else{
                            return response()->json(['message' => 'coupon Already Used',], 400);
                        }
                        }
                        $amount = 0;
                        if($coupon[0]->discount_type == 4){
                        $amount = ($value/100) * $coupon[0]->amount;
                        }
                        else{
                            $amount = $coupon[0]->amount;
                        }
                        return response()->json([
                            'message' => 'Coupon Updated Succssfully',
                            'coupon_id' => $coupon[0]->id,
                            'discount'=>$amount
                        ], 200);

                    }
                }else{
                    if($coupon[0]->limit_per_user !=null){
                        $booking_count = booking::where('customer_id',$id)->where('coupon_id',$coupon[0]->id)->get();
                        if(count($booking_count)< $coupon[0]->limit_per_user){
                            $amount = 0;
                            if($coupon[0]->discount_type == 4){
                            $amount = ($value/100) * $coupon[0]->amount;
                            }
                            else{
                                $amount = $coupon[0]->amount;
                            }
                            return response()->json([
                                'message' => 'Coupon Updated Succssfully',
                                'coupon_id' => $coupon[0]->id,
                                'discount'=>$amount
                            ], 200);
                        }else{
                            return response()->json(['message' => 'coupon Already Used',], 400);
                        }
                    }
                    $amount = 0;
                    if($coupon[0]->discount_type == 4){
                    $amount = ($value/100) * $coupon[0]->amount;
                    }
                    else{
                        $amount = $coupon[0]->amount;
                    }
                    return response()->json([
                        'message' => 'Coupon Updated Succssfully',
                        'coupon_id' => $coupon[0]->id,
                        'discount'=>$amount
                    ], 200);
                }
            }
            return response()->json(['message' => 'coupon expired',], 400);
        }else{
            return response()->json(['message' => 'Cart Value Not Enough',], 400);
        }
    // }else{
    //     return response()->json(['message' => 'Coupon Code Not for this Shop',], 400);
    // }
}else{
    return response()->json(['message' => 'invalid coupon code',], 400);
}
    
}

public function sendNotificationCustomer($msg,$customer_id){
    $customer = customer::find($request->customer_id);
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS =>"{\r\n\"to\":\"$customer->firebase_key\",\r\n \"notification\" : {\r\n  \"sound\" : \"default\",\r\n  \"body\" :  \"$msg\",\r\n  \"title\" : \"\",\r\n  \"content_available\" : true,\r\n  \"priority\" : \"high\"\r\n },\r\n \"data\" : {\r\n  \"sound\" : \"default\",\r\n  \"body\" :  \"$msg\",\r\n  \"title\" : \"\",\r\n  \"content_available\" : true,\r\n  \"priority\" : \"high\"\r\n }\r\n}",
    CURLOPT_HTTPHEADER => array(
        "Authorization: key=AAAA7jZoJ5c:APA91bHWucHex7yFNIWOeB8acb2hL6EFHCi9taaIW6ddhqUQmY4g3Jy-2U3gLFPwcAZfIAPpaYUtNZeCoY_bMxZ7hemVj5Jufef6r1oMYQf7yKvL449ax1nKdUKlJ3EhFhjV09FETxrC",
        "Content-Type: application/json"
    ),
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
}

public function sendNotificationAgent($msg){
    $agent = agent::where('status',0)->get();
    foreach($agent as $row){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>"{\r\n\"to\":\"$row->firebase_key\",\r\n \"notification\" : {\r\n  \"sound\" : \"default\",\r\n  \"body\" :  \"$msg\",\r\n  \"title\" : \"\",\r\n  \"content_available\" : true,\r\n  \"priority\" : \"high\"\r\n },\r\n \"data\" : {\r\n  \"sound\" : \"default\",\r\n  \"body\" :  \"$msg\",\r\n  \"title\" : \"\",\r\n  \"content_available\" : true,\r\n  \"priority\" : \"high\"\r\n }\r\n}",
        CURLOPT_HTTPHEADER => array(
            "Authorization: key=AAAA7jZoJ5c:APA91bHWucHex7yFNIWOeB8acb2hL6EFHCi9taaIW6ddhqUQmY4g3Jy-2U3gLFPwcAZfIAPpaYUtNZeCoY_bMxZ7hemVj5Jufef6r1oMYQf7yKvL449ax1nKdUKlJ3EhFhjV09FETxrC",
            "Content-Type: application/json"
        ),
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
    }
}

    public function savebooking(Request $request){
        try{
            $config = [
                'table' => 'bookings',
                'field' => 'booking_id',
                'length' => 10,
                'prefix' => 'OC-'
            ];
            $booking_id = IdGenerator::generate($config);
            $randomid = mt_rand(1000,9999); 
            $booking = new booking;
            $booking->date = date('Y-m-d');
            $booking->booking_id = $booking_id;
            //$booking->shop_id = $request->shop_id;
            $booking->customer_id = $request->customer_id;
            $booking->booking_date = date('Y-m-d',strtotime($request->booking_date));
            $booking->booking_time = $request->booking_time;
            $booking->coupon_id = $request->coupon_id;
            $booking->coupon_code = $request->coupon_code;
            $booking->coupon_value = $request->coupon_value;
            $booking->subtotal = $request->subtotal;
            // $booking->membership_name = $request->membership_name;
            // $booking->membership_value = $request->membership_value;
            $booking->total = $request->total;
            $booking->otp = $randomid;
            //$booking->address_id = $request->address_id;
            $booking->latitude = $request->latitude;
            $booking->longitude = $request->longitude;
            $booking->address = $request->address;
            $booking->vehicle_id = $request->vehicle_id;
            $booking->service_charge = $request->service_charge;
            // $booking->payment_type = $request->payment_type;
            $booking->payment_type = 1;
            $booking->payment_status = 1;
            $booking->payment_id = $request->payment_id;

            $booking->status = 0;
            $booking->save();
            //$shop = User::find($request->shop_id);
            //$customer=customer::find($request->customer_id);
            
            //$msg= "Dear Customer, Please use the code ".$booking->otp." to Approve your ".$shop->busisness_name;
            
            return response()->json(
            ['message' => 'Save Successfully',
            'booking_id'=>$booking->id,
            ], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function savebookingservice(Request $request){
        try{
            $service = service::find($request->service_id);

            $booking_service = new booking_service;
            $booking_service->booking_id = $request->booking_id;
            $booking_service->service_id = $request->service_id;
            $booking_service->service_name_english = $service->service_name_english;
            $booking_service->service_name_arabic = $service->service_name_arabic;
            $booking_service->price = $request->price;
            $booking_service->save();
        return response()->json(
            ['message' => 'Save Successfully'],
            200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }

    public function savetransactionhistory(Request $request){
        try{
            $transaction_history = new transaction_history;
            $transaction_history->booking_id = $request->booking_id;
            $transaction_history->invoicereference = $request->invoicereference;
            $transaction_history->transactiondate = $request->transactiondate; 
            $transaction_history->paymentgateway = $request->paymentgateway; 
            $transaction_history->referenceid = $request->referenceid;
            $transaction_history->trackid = $request->trackid;
            $transaction_history->transactionid = $request->transactionid;
            $transaction_history->paymentid = $request->paymentid;
            $transaction_history->authorizationid = $request->authorizationid;
            $transaction_history->transactionstatus = $request->transactionstatus;
            $transaction_history->transationvalue = $request->transationvalue;
            $transaction_history->customerservicecharge = $request->customerservicecharge;
            $transaction_history->duevalue = $request->duevalue;
            $transaction_history->paidcurrency = $request->paidcurrency;
            $transaction_history->paidcurrencyvalue = $request->paidcurrencyvalue;
            $transaction_history->currency = $request->currency;
            $transaction_history->error = $request->error;
            $transaction_history->cardnumber = $request->cardnumber;
            $transaction_history->save();
        return response()->json(
            ['message' => 'Save Successfully'],
            200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        } 
    }


    public function getallbooking($id){
        $booking = booking::where('customer_id',$id)->orderBy('id','DESC')->get();
        $datas =array();
        foreach ($booking as $key => $value) {
            $vehicle = vehicles::find($value->vehicle_id);
            $data = array(
                'booking_id' => $value->id,
                // 'cover_image' => $shop->cover_image,
                // 'shop_address' => $shop->address,
                // 'shop_name' => $shop->busisness_name,
                //'phone' => $shop->mobile,
                'booking_date' => $value->booking_date,
                'booking_time' => $value->booking_time,
                'booking_status' => (int)$value->booking_status,
                'payment_type' => (int)$value->payment_type,
                'payment_status' => (int)$value->payment_status,
                'otp' => $value->otp,
                'subtotal' => $value->subtotal,
                'discount' => (string)$value->coupon_value,
                //'membership_value' => (string)$value->membership_value,
                'total' => $value->total,
                'coupon_code' => '',
                'coupon_value' => 0.0,
                'booking_address'=> $value->address,
                'vehicle_id'=> (int)$value->vehicle_id,
                'vehicle_name'=> $vehicle->brand.' '.$vehicle->vehicle_name,
                'vehicle_no'=> $vehicle->registration_city.' '.$vehicle->registration_code.' '.$vehicle->registration_number,
            );
            // if(empty($shop->busisness_name)){
            //     $data['shop_name'] = $shop->name;
            // }
            if($value->coupon_code !=null){
                $data['coupon_code'] = $value->coupon_code;
            }
            if($value->coupon_value !=null){
                $data['coupon_value'] = $value->coupon_value;
            }
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function bookingmail($id){
        $booking = booking::find($id);
        $booking_service = booking_service::where('booking_id',$id)->get();
        // $booking_package = booking_package::where('booking_id',$id)->get();
        // $booking_product = booking_product::where('booking_id',$id)->get();
        //$shop = User::find($booking->shop_id);
        $customer = customer::find($booking->customer_id);

        Mail::send('mail.invoice',compact('customer','booking','booking_service'),function($message) use($customer){
            $message->to($customer['email'])->subject('Booking Details');
            //$message->from('mail.lrbinfotech@gmail.com','Obsessive Crew Website');
        });

        // $pdf = PDF::loadView('print.booking_print',compact('booking_service','booking','booking_product','booking_package','customer','shop'));
        // $pdf->setPaper('A4');
        // return $pdf->stream('report.pdf');
    }


    public function getbooking($id){
        $booking = booking::where('id',$id)->get();
        $data=array();
        $datas=array();
        foreach ($booking as $key => $value) {
            //$shop = User::find($value->shop_id);
            $vehicle = vehicles::find($value->vehicle_id);
            $data = array(
                '_id' => $value->id,
                'booking_id' => $value->booking_id,
                //'cover_image' => $shop->cover_image,
                //'profile_image' => $shop->profile_image,
                // 'lat' => (string)$shop->latitude,
                // 'lng' => (string)$shop->longitude,
                'service_type' => '',
                // 'address' => $shop->address,
                // 'shop_name' => $shop->busisness_name,
                // 'phone' => $shop->mobile,
                'booking_date' => $value->booking_date,
                'booking_time' => $value->booking_time,
                'booking_status' => (int)$value->status,
                'payment_type' => (int)$value->payment_type,
                'payment_status' => (int)$value->payment_status,
                'otp' => $value->otp,
                'subtotal' => $value->subtotal,
                'total' => $value->total,
                'discount' => (string)$value->coupon_value,
                'coupon_code' => '',
                'coupon_value' => 0.0,
                'address_id'=> (int)$value->address_id,
                //'membership_value' => $value->membership_value,
                'vehicle_id'=> (int)$value->vehicle_id,
                'vehicle_name'=> $vehicle->brand.' '.$vehicle->vehicle_name,
                'vehicle_no'=> $vehicle->registration_city.' '.$vehicle->registration_code.' '.$vehicle->registration_number,
                'status' => '',
                // 'pickup_driver_name' => '',
                // 'pickup_driver_mobile' => '',
                // 'pickup_driver_email' => '',
                // 'delivery_driver_name' => '',
                // 'delivery_driver_mobile' => '',
                // 'delivery_driver_email' => '',
                // 'towing_service' => 0,
            );
            

            if($value->status == 0){
                $data['status'] = 'Booking Accepted';
            }
            elseif($value->status == 1){
                $data['status'] = 'Processing';
            }
            elseif($value->status == 2){
                $data['status'] = 'Completed';
            }

            // if(empty($shop->busisness_name)){
            //     $data['shop_name'] = $shop->name;
            // }
            if($value->coupon_code !=null){
                $data['coupon_code'] = $value->coupon_code;
            }
            if($value->coupon_value !=null){
                $data['coupon_value'] = (string)$value->coupon_value;
            }
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function getbookingservice($id){
        $booking = booking_service::where('booking_id',$id)->get();
        $data =array();
        if(count($booking) >0){
            foreach ($booking as $key => $value) {
                $service = service::find($value->service_id);
                $data = array(
                    'booking_id' => $value->id,
                    'service_image' => $service->image,
                    'service_name_english' => $service->service_name_english,
                    'service_name_arabic' => $service->service_name_arabic,
                    'price' => $value->price,
                );
                $datas[] = $data;
            }
        }else{
            $datas=array();
        }
        return response()->json($datas); 
    }

    public function getweeks(){
        date_default_timezone_set("Asia/Dubai");
        date_default_timezone_get();
        $week = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

        $today = date("l");

        $day = date('Y-m-d', strtotime(' +1 day'));

        $tomorrow = date('l', strtotime($day));

        $third1 = date('Y-m-d', strtotime(' +2 day'));
        $third = date('l', strtotime($third1));
        $four1 = date('Y-m-d', strtotime(' +3 day'));
        $four = date('l', strtotime($four1));
        $five1 = date('Y-m-d', strtotime(' +4 day'));
        $five = date('l', strtotime($five1));
        $six1 = date('Y-m-d', strtotime(' +5 day'));
        $six = date('l', strtotime($six1));
        $seven1 = date('Y-m-d', strtotime(' +6 day'));
        $seven = date('l', strtotime($seven1));

        foreach ($week as $key => $value) {
            if($today == $value){
                $weeks = "Today";
                $data[] = array(
                'weeks' => $weeks,
                'days' => date('M d', strtotime($today)),
                'month' => date('M', strtotime($today)),
                'date' => date('d', strtotime($today)),
                );
            }
        }
        foreach ($week as $key => $value) {
            if($tomorrow == $value){
                $weeks = "Tomorrow";
                $data[] = array(
                'weeks' => $weeks,
                'days' => date('M d', strtotime($tomorrow)),
                'month' => date('M', strtotime($tomorrow)),
                'date' => date('d', strtotime($tomorrow)),
                );
            }           
        }
        foreach ($week as $key => $value) {
            if($third == $value){
                $data[] = array(
                'weeks' => $value,
                'days' => date('M d', strtotime($third)),
                'month' => date('M', strtotime($third)),
                'date' => date('d', strtotime($third)),
                );
            }
        }
        foreach ($week as $key => $value) {
            if($four == $value){
                $data[] = array(
                'weeks' => $value,
                'days' => date('M d', strtotime($four)),
                'month' => date('M', strtotime($four)),
                'date' => date('d', strtotime($four)),
                );
            }
        }
        foreach ($week as $key => $value) {
            if($five == $value){
                $data[] = array(
                'weeks' => $value,
                'days' => date('M d', strtotime($five)),
                'month' => date('M', strtotime($five)),
                'date' => date('d', strtotime($five)),
                );
            }
        }
        foreach ($week as $key => $value) {
            if($six == $value){
                $data[] = array(
                'weeks' => $value,
                'days' => date('M d', strtotime($six)),
                'month' => date('M', strtotime($six)),
                'date' => date('d', strtotime($six)),
                );
            }
        }
        foreach ($week as $key => $value) {
            if($seven == $value){
                $data[] = array(
                'weeks' => $value,
                'days' => date('M d', strtotime($seven)),
                'month' => date('M', strtotime($seven)),
                'date' => date('d', strtotime($seven)),
                );
            } 
        }
        return response()->json($data);
    }

    public function getavailabletimetoday(){
        try{
            date_default_timezone_set("Asia/Dubai");
            date_default_timezone_get();
            $today = date("l");
            $time = date("h:i A"); 
            $data = array();
    
            $times = array('08:00 AM','09:00 AM','10:00 AM','11:00 AM','12:00 PM','01:00 PM','02:00 PM','03:00 PM','04:00 PM','05:00 PM','06:00 PM','07:00 PM','08:00 PM');
            $hourplus=date("h:00 A",strtotime('+2 hours',strtotime($times[0]))); 
            foreach ($times as $key => $row) {
                if(strtotime($time) < strtotime($row)){
                    if(strtotime($row) >= strtotime($hourplus)){
                    $hourplus = date("h:00 A",strtotime('+2 hours',strtotime($row))); 
                    $data[] = $row .' - '.$hourplus;
                    }
                }
            }
            return response()->json($data);

        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        }
    }

    public function getminutestime($time){
        try{
            // date_default_timezone_set("Asia/Dubai");
            // date_default_timezone_get();
            // $today = date("l");
            // $time = date("h:i A"); 
            $data = array();
            $new_time = date('h A',strtotime($time));
            $data[] = date('h:i A',strtotime($new_time));
            for($i=0;$i<5;$i++) {
                $new_time = date('h:i A',strtotime('+10 minutes',strtotime($new_time)));
                $data[] = $new_time;
            }
            return response()->json($data);

        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        }
    }


    public function getavailabletime(){
        try{
            date_default_timezone_set("Asia/Dubai");
            date_default_timezone_get();
            $today = date("l");
            $time = date("h:i A"); 
            $data = array();
    
            // $times = array('00:00 AM','01:00 AM','02:00 AM','03:00 AM','04:00 AM','05:00 AM','06:00 AM','07:00 AM','08:00 AM','09:00 AM','10:00 AM','11:00 AM','12:00 PM','01:00 PM','02:00 PM','03:00 PM','04:00 PM','05:00 PM','06:00 PM','07:00 PM','08:00 PM','09:00 PM','10:00 PM','11:00 PM');
            $times = array('08:00 AM','09:00 AM','10:00 AM','11:00 AM','12:00 PM','01:00 PM','02:00 PM','03:00 PM','04:00 PM','05:00 PM','06:00 PM','07:00 PM','08:00 PM','09:00 PM');
            $hourplus=date("h:00 A",strtotime('+2 hours',strtotime($times[0]))); 
            $data[] = $times[0] .' - '.$hourplus;
            foreach ($times as $key => $row) {
                if(strtotime($row) >= strtotime($hourplus)){
                $hourplus = date("h:00 A",strtotime('+2 hours',strtotime($row))); 
                $data[] = $row .' - '.$hourplus;
                }
            }
            return response()->json($data);

        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(),'status'=>400], 400);
        }
    }

    public function getcouponcode($id){
        $coupon = coupon::where('status',1)->get();
        //return response()->json($coupon); 
        $data =array();
        $datas =array();
        foreach ($coupon as $key => $value) {
            if(empty($value->user_type) && date('Y-m-d') < $value->end_date){
                $data = array(
                    'coupon_id' => $value->id,
                    'coupon_code' => $value->coupon_code,
                    'description' => $value->description,
                    'start_date' => $value->start_date,
                    'end_date' => $value->end_date,
                    'discount_type' => $value->discount_type,
                    'amount' => $value->amount,
                    'user_type' => 0,
                );
                $datas[] = $data;
            }
            else{
                $coupon1  = coupon::find($value->id);
                $arraydata=array();
                foreach(explode(',',$coupon1->user_id) as $user1){
                    if($user1 == $id && date('Y-m-d') < $value->end_date){
                        $data = array(
                            'coupon_id' => $value->id,
                            'coupon_code' => $value->coupon_code,
                            'description' => $value->description,
                            'start_date' => $value->start_date,
                            'end_date' => $value->end_date,
                            'discount_type' => $value->discount_type,
                            'amount' => $value->amount,
                            'user_type' => 1,
                        );
                        $datas[] = $data;
                    }
                }
            }
        }   
        return response()->json($datas); 
    }


    public function getbookingtransaction($id){
        $booking = booking::where('customer_id',$id)->orderBy('id','DESC')->get();
        $data =array();
        $datas =array();
        foreach ($booking as $key => $value) {
            if($value->payment_type == '1' && $value->payment_status == '0'){
            }
            else{
             $dateTime = new Carbon($value->updated_at, new \DateTimeZone('Asia/Dubai'));
            $data = array(
                '_id' => $value->id,
                'booking_id' => $value->booking_id,
                'date' => $dateTime->diffForHumans(),
                'payment_id' => $value->payment_id,
                'total' => $value->total,
                'payment_type' => (int)$value->payment_type,
                'payment_status' => (int)$value->payment_status,
            );
            if($value->payment_id == 0){
                $data['payment_id'] = '0';
            }
            if($value->payment_id == 1){
                $data['payment_id'] = $value->order_id;
            }
            $datas[] = $data;
            }
        }   
        return response()->json($datas); 
    }

    public function getshareurl()
    {
        $data = array('https://apps.apple.com/ae/app/isalon-uae-app/id1537638428','https://play.google.com/store/apps/details?id=com.isalon.isalonapp');
        return response()->json($data);
    }

    public function savechatadmin(Request $request){
        date_default_timezone_set("Asia/Dubai");
        date_default_timezone_get();
        $admin_customer = new admin_customer;
        $admin_customer->message = $request->message;
        $admin_customer->customer_id = $request->customer_id;
        $admin_customer->date = date('Y-m-d');
        $admin_customer->time = date('h:i A');
        $admin_customer->message_from = 0;
        if( $request->message != ''){
        $admin_customer->save();
            //$this->sendChatNotification($admin_customer->id);
            // $dateTime = new Carbon($admin_customer->updated_at, new \DateTimeZone('Asia/Dubai'));
            // $message =  array(
            //     'message'=> $admin_customer->message,
            //     'message_from'=> 0,
            //     'date'=> $dateTime->diffForHumans(),
            //     'channel_name'=> $request->customer_id,
            // );
            // event(new ChatAdmin($message));
        }
        return response()->json(
        ['message' => 'Save Successfully'],
        200);
    }

    public function getchatadmin($id){
        date_default_timezone_set("Asia/Dubai");
        date_default_timezone_get();
        $chat = admin_customer::where('customer_id',$id)->orderBy('id','DESC')->get();
        $data =array();
        $datas =array();
        foreach ($chat as $key => $value) {
            $dateTime = new Carbon($value->updated_at, new \DateTimeZone('Asia/Dubai'));
            $data = array(
                //'admin_id' => $value->admin_id,
                'message' => $value->message,
                'message_from' => (int)$value->message_from,
                'time' => $dateTime->diffForHumans(),
            );
            $datas[] = $data;
        }   
        return response()->json($datas); 
    }

    public function adminchatcount($id){
        date_default_timezone_set("Asia/Dubai");
        date_default_timezone_get();
        $chat = admin_customer::where('booking_id',$id)->orderBy('id','DESC')->get();
        $chat_count=0;

        foreach ($chat as $key => $value) {
            if($value->message_from == 0){
            break;
            }
            $chat_count++;
        }   
        return response()->json([
            'chat_count'=>$chat_count
        ], 200);
    }


}
