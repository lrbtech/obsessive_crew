<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'customer'],function(){

    //customer
    Route::post('/create-customer', 'Api\UserController@createCustomer');
    Route::post('/verify-customer', 'Api\UserController@verifyCustomer');
    Route::post('/login', 'Api\UserController@customerLogin');
    Route::post('/update-customer', 'Api\UserController@updateCustomer');
    Route::get('/edit-customer/{id}', 'Api\UserController@editCustomer');
    Route::post('/update-firebasekey', 'Api\UserController@updatefirebasekey');

    Route::get('/sendwelcomemail/{id}', 'Api\UserController@sendwelcomemail');

    Route::post('/otp-resend', 'Api\UserController@getApiOtpResend');

    Route::post('/forget-password', 'Api\UserController@forgetPassword');
    Route::post('/reset-password', 'Api\UserController@resetPassword');

    Route::get('/get-city', 'Api\UserController@getcity');
    Route::get('/get-brand', 'Api\UserController@getbrand');
    Route::get('/get-vehicle-model/{brand}', 'Api\UserController@getvehiclemodel');
    Route::get('/get-vehicle-type', 'Api\UserController@getvehicletype');
    Route::get('/get-colours', 'Api\UserController@getcolours');

    Route::post('/save-vehicles', 'Api\UserController@savevehicles');
    Route::post('/update-vehicles', 'Api\UserController@updatevehicles');
    Route::get('/get-vehicles/{user_id}', 'Api\UserController@getvehicles');
    Route::get('/edit-vehicles/{id}', 'Api\UserController@editvehicles');
    Route::get('/delete-vehicles/{id}', 'Api\UserController@deletevehicles');

    Route::post('/save-card', 'Api\UserController@savecard');
    Route::post('/update-card', 'Api\UserController@updatecard');
    Route::get('/get-card/{customer_id}', 'Api\UserController@getcard');
    Route::get('/edit-card/{id}', 'Api\UserController@editcard');
    Route::get('/delete-card/{id}', 'Api\UserController@deletecard');

    // Route::get('/get-address/{user_id}', 'Api\UserController@getaddress');
    // Route::get('/show-address/{user_id}', 'Api\UserController@showaddress');
    // Route::post('/save-address', 'Api\UserController@saveaddress');
    // Route::post('/update-address', 'Api\UserController@updateaddress');
    // Route::get('/delete-address/{id}', 'Api\UserController@deleteaddress');

    Route::get('/get-category/{type}', 'Api\UserController@getcategory');

    //shops
    Route::get('/get-all-shops/{latitude}/{longitude}/{category}/{date}/{time}/{type}', 'Api\UserController@getallshops');

    Route::get('/get-shop-reviews/{shop_id}', 'Api\UserController@getshopreviews');

    Route::get('/get-service', 'Api\UserController@getservice');
    Route::get('/get-sub-service', 'Api\UserController@getsubservice');
    Route::get('/get-package/{shop_id}', 'Api\UserController@getpackage');
    Route::get('/get-package-services/{id}', 'Api\UserController@getpackageservices');
    Route::get('/get-product/{shop_id}', 'Api\UserController@getproduct');

    Route::get('/coupon-apply/{id}/{code}/{value}', 'Api\UserController@couponapply');


    //booking
    Route::post('/save-booking', 'Api\UserController@savebooking');
    Route::post('/save-booking-service', 'Api\UserController@savebookingservice');

    Route::post('/save-transaction-history', 'Api\UserController@savetransactionhistory');

    Route::get('/booking-mail/{id}', 'Api\UserController@bookingmail');

    Route::get('/get-membership/{customer_id}', 'Api\UserController@getmembership');

    Route::get('/get-all-booking/{customer_id}', 'Api\UserController@getallbooking');
    Route::get('/get-booking/{id}', 'Api\UserController@getbooking');
    Route::get('/get-booking-service/{id}', 'Api\UserController@getbookingservice');

    Route::get('/get-booking-transaction/{id}', 'Api\UserController@getbookingtransaction');

    Route::get('/get-weeks', 'Api\UserController@getweeks');
    Route::get('/get-available-time-today', 'Api\UserController@getavailabletimetoday');
    Route::get('/get-available-time', 'Api\UserController@getavailabletime');
    Route::get('/get-minutes-time/{time}', 'Api\UserController@getminutestime');

    Route::get('/get-all-package', 'Api\UserController@getallpackage');
    Route::get('/get-coupon-code/{customer_id}', 'Api\UserController@getcouponcode');

    Route::get('/get-terms/{lang}', 'Api\UserController@getterms');
    Route::get('/get-privacy/{lang}', 'Api\UserController@getprivacy');
    Route::get('/get-about/{lang}', 'Api\UserController@getabout');

    Route::get('/get-notification/{customer_id}', 'Api\UserController@getnotification');

    Route::get('/get-share-url', 'Api\UserController@getshareurl');

    //chat admin
    Route::get('/get-chat-admin/{id}', 'Api\UserController@getchatadmin');
    Route::post('/save-chat-admin', 'Api\UserController@savechatadmin');
    Route::get('/admin-chat-count/{id}', 'Api\UserController@adminchatcount');

});


Route::group(['prefix' => 'agent'],function(){

    Route::post('/login', 'Api\AgentController@agentLogin');

    Route::get('/dashboard/{id}', 'Api\AgentController@dashboard');

    Route::get('/get-today-booking/{id}', 'Api\AgentController@gettodaybooking');
    Route::get('/get-pending-booking/{id}', 'Api\AgentController@getpendingbooking');
    Route::get('/get-upcoming-booking/{id}', 'Api\AgentController@getupcomingbooking');
    Route::get('/get-booking-completed/{id}', 'Api\AgentController@getbookingcompleted');
    Route::get('/get-booking/{id}', 'Api\AgentController@getbooking');
    Route::get('/get-all-booking/{id}', 'Api\AgentController@getallbooking');
    Route::get('/get-booking-details/{id}', 'Api\AgentController@getbookingdetails');
    Route::get('/get-booking-service/{id}', 'Api\AgentController@getbookingservice');

    Route::post('/accept-booking', 'Api\AgentController@acceptbooking');
    Route::post('/update-booking-status', 'Api\AgentController@updatebookingstatus');
    Route::get('/update-booking-paid/{booking_id}', 'Api\AgentController@updatebookingpaid');

    Route::get('/get-notification/{id}', 'Api\AgentController@getnotification');

    Route::post('/change-password', 'Api\AgentController@changePassword');

    Route::post('/booking-otp-verified', 'Api\AgentController@bookingotpverified');

    Route::post('/otp-resend', 'Api\AgentController@getApiOtpResend');

    Route::post('/forget-password', 'Api\AgentController@forgetPassword');
    Route::post('/reset-password', 'Api\AgentController@resetPassword');

});