<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\shop_service;
use App\User;
use App\agent;
use App\customer;
use App\booking;
use Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        date_default_timezone_set("Asia/Dubai");
        date_default_timezone_get();
    }

    public function dashboard(){
        $today = date('Y-m-d');
        $cfdate = date('Y-m-d',strtotime('first day of this month'));
        $cldate = date('Y-m-d',strtotime('last day of this month'));
  
        $booking = booking::count();
        $booking_value = booking::all()->sum("total");
        $agent = agent::count();
        $customer = customer::count();

        $current_month_customer = customer::whereBetween('date', [$cfdate, $cldate])->count();
        $current_month_booking_value = booking::whereBetween('date', [$cfdate, $cldate])->get()->sum("total");
        $current_month_booking = booking::whereBetween('date', [$cfdate, $cldate])->count();

        return view('admin.dashboard',compact('booking','booking_value','customer','agent'));
    }
}
