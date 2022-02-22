<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\agent;
use App\admin;
use App\customer;
use App\brand;
use App\vehicle_type;
use App\vehicles;
use App\manage_address;
use App\city;
use App\service;
use App\booking;
use App\booking_service;
use App\shop_service;
use App\shop_package;
use App\shop_product;
use App\shop_time;
use App\service_price;
use App\app_settings;
use App\colour;
use App\coupon;
use App\push_notification;
use Hash;
use Auth;
use DB;
use Validator;
use Mail;
use PDF;
use Carbon\Carbon;

class AgentController extends Controller
{
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

    public function agentLogin(Request $request){
        $exist = agent::where('email',$request->email)->get();
        if(count($exist)>0){
            if($exist[0]->status == 0){
                if(Hash::check($request->password,$exist[0]->password)){
                    $agent = agent::find($exist[0]->id);
                    $agent->firebase_key = $request->firebase_key;
                    $agent->save();
                
                // $agent_type='';
                // if($exist[0]->role_id == 'admin'){
                //     $agent_type=1;
                // }
                // elseif($exist[0]->role_id == '2'){
                //     $agent_type=2;
                // }
                // elseif($exist[0]->role_id == '3'){
                //     $agent_type=3;
                // }
                // elseif($exist[0]->role_id == '4'){
                //     $agent_type=4;
                // }

                return response()->json(['message' => 'Login Successfully',
                '_id'=>$exist[0]->id,
                //'shop_id'=>(int)$exist[0]->user_id,
                'name'=>$exist[0]->name,
                //'shop_name'=>$exist[0]->busisness_name,
                //'cover_image'=>$exist[0]->cover_image,
                //'agent_type'=>(int)$agent_type,
                ], 200);
                }else{
                    return response()->json(['message' => 'Records Does not Match','status'=>403], 403);
                }
            }else{
                return response()->json(['message' => 'Verify Your Account','status'=>401,'agent_id'=>$exist[0]->id], 401);
            }
        }else{
            return response()->json(['message' => 'Email Address Not Registered','status'=>404], 404);
        }
    }

    public function updatebookingstatus($booking_id,$status){
        $booking = booking::find($booking_id);
        $booking->status = $status;
        $booking->save();

        $booking = booking::find($booking_id);
        $booking_service = booking_service::where('booking_id',$booking_id)->get();
        $customer = customer::find($booking->customer_id);

        if($status == 5){
            Mail::send('mail.invoice',compact('customer','booking','booking_service'),function($message) use($customer){
                $message->to($customer['email'])->subject('Booking Details');
                //$message->from('mail.lrbinfotech@gmail.com','Auto Wash Website');
            });
        }

        return response()->json(['message' => 'Update Successfully'], 200);
    }

    public function updatebookingpaid($booking_id){
        $booking = booking::find($booking_id);
        $booking->payment_status = 1;
        $booking->save();
        return response()->json(['message' => 'Update Successfully'], 200);
    }

    public function gettodaybooking(){
        $today = date('Y-m-d');
        $booking = booking::where('date',$today)->orderBy('id','DESC')->get();
        $datas =array();
        foreach ($booking as $key => $value) {
            $vehicle = vehicles::find($value->vehicle_id);
            $data = array(
                'booking_id' => $value->id,
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


    public function getallbooking(){
        $booking = booking::orderBy('id','DESC')->get();
        $datas =array();
        foreach ($booking as $key => $value) {
            $vehicle = vehicles::find($value->vehicle_id);
            $data = array(
                'booking_id' => $value->id,
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

    public function getbooking($id){
        $booking = booking::where('id',$id)->get();
        $data=array();
        $datas=array();
        foreach ($booking as $key => $value) {
            $vehicle = vehicles::find($value->vehicle_id);
            $data = array(
                '_id' => $value->id,
                'booking_id' => $value->booking_id,
                'service_type' => '',
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
                $data['status'] = 'Order Placed';
            }
            elseif($value->status == 1){
                $data['status'] = 'Order Accepted';
            }
            elseif($value->status == 2){
                $data['status'] = 'Received';
            }
            elseif($value->status == 3){
                $data['status'] = 'Processing';
            }
            elseif($value->status == 4){
                $data['status'] = 'Delivered';
            }

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


    public function getbookingcompleted(){
        $today = date('Y-m-d');
        $booking = booking::where('status',4)->orderBy('id','DESC')->get();
        $data =array();
        $datas =array();
        foreach ($booking as $key => $value) {
            $customer = customer::find($value->customer_id);
            $vehicle = vehicles::find($value->vehicle_id);
            $data = array(
                'id' => $value->id,
                'booking_id' => (string)$value->booking_id,
                'customer_name' => $customer->first_name .' '. $customer->last_name,
                'customer_mobile' => $customer->mobile,
                'customer_email' => $customer->email,
                'booking_date' => $value->booking_date,
                'booking_time' => $value->booking_time,
                'status' => '',
                'payment_type' => (int)$value->payment_type,
                'payment_status' => (int)$value->payment_status,
                'otp' => $value->otp,
                'subtotal' => $value->subtotal,
                'total' => $value->total,
                'coupon_code' => '',
                'coupon_value' => (string)0.0,
                'booking_type' => '',
                'latitude' => '',
                'longitude' => '',
                'address' => '',
                'booking_status' => (int)$value->status,
            );
            if($value->coupon_code !=null){
                $data['coupon_code'] = $value->coupon_code;
            }
            if($value->coupon_value !=null){
                $data['coupon_value'] = $value->coupon_value;
            }
            if($value->status == 0){
                $data['status'] = 'Order Placed';
            }
            elseif($value->status == 1){
                $data['status'] = 'Order Accepted';
            }
            elseif($value->status == 2){
                $data['status'] = 'Received';
            }
            elseif($value->status == 3){
                $data['status'] = 'Processing';
            }
            elseif($value->status == 4){
                $data['status'] = 'Completed';
            }
            elseif($value->status == 5){
                $data['status'] = 'Delivered';
            }
            $datas[] = $data;
        }   
        return response()->json($datas);
    }

    public function getbookingdetails($id){
        $booking = booking::where('id',$id)->get();
        $data =array();
        $datas =array();
        foreach ($booking as $key => $value) {
            $customer = customer::find($value->customer_id);
            $vehicle = vehicles::find($value->vehicle_id);
            $data = array(
                'id' => $value->id,
                'bookingid' => (string)$value->booking_id,
                'customer_name' => $customer->first_name .' '. $customer->last_name,
                'customer_mobile' => $customer->mobile,
                'customer_email' => $customer->email,
                'booking_date' => $value->booking_date,
                'booking_time' => $value->booking_time,
                'status' => '',
                'payment_type' => (int)$value->payment_type,
                'payment_status' => (int)$value->payment_status,
                'otp' => $value->otp,
                'subtotal' => $value->subtotal,
                'total' => $value->total,
                'coupon_code' => '',
                'coupon_value' => (string)0.0,
                'vehicle_name'=> $vehicle->vehicle_name,
                'vehicle_no'=> $vehicle->registration_city .' '. $vehicle->registration_code .' '. $vehicle->registration_number,
                'booking_type' => '',
                'latitude' => '',
                'longitude' => '',
                'address' => '',
                'booking_status' => (int)$value->status,
            );
            if($value->coupon_code !=null){
                $data['coupon_code'] = $value->coupon_code;
            }
            if($value->coupon_value !=null){
                $data['coupon_value'] = $value->coupon_value;
            }

            if($value->status == 0){
                $data['status'] = 'Order Placed';
            }
            elseif($value->status == 1){
                $data['status'] = 'Order Accepted';
            }
            elseif($value->status == 2){
                $data['status'] = 'Received';
            }
            elseif($value->status == 3){
                $data['status'] = 'Processing';
            }
            elseif($value->status == 4){
                $data['status'] = 'Completed';
            }
            elseif($value->status == 5){
                $data['status'] = 'Delivered';
            }
            $datas[] = $data;
        }   
        return response()->json($datas);
    }


    public function dashboard(){
        $today = date('Y-m-d');
        $total_service = service::count();
        // $total_product = shop_product::count();
        // $total_package = shop_package::count();
        $total_booking = booking::count();
        $today_booking = booking::where('date',$today)->count();
        $future_booking = booking::where('date','>',$today)->count();
        $open_booking = booking::where('status',1)->count();

        $data =array();
        $data = array(
            'total_service' => $total_service,
            // 'total_product' => $total_product,
            // 'total_package' => $total_package,
            'total_booking' => $total_booking,
            'today_booking' => $today_booking,
            'future_booking' => $future_booking,
            'open_booking' => $open_booking,
        );
        return response()->json($data);  
    }



    public function getnotification($id){
        $data = push_notification::where('status',1)->where('send_to',1)->get();
        $data1 = push_notification::where('status',1)->where('send_to',3)->get();
        $datas=array();
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
            foreach(explode(',',$value->shop_ids) as $shop1){
                $arraydata[]=$shop1;
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

    public function changePassword(Request $request){
        $agent = agent::find($request->agent_id);
        $hashedPassword = $agent->password;
 
        if (\Hash::check($request->oldpassword , $hashedPassword )) {
            if (!\Hash::check($request->password , $hashedPassword)) {
                $agent->password = Hash::make($request->password);
                $agent->save();
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


}
