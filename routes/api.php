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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// start LeveltypeapiController

Route::GET('v1/getbuyuserlist', [App\Http\Controllers\Api\BuyUserApiController::class, 'getbuyuserlist']);
Route::GET('v1/get_dashboard_count', [App\Http\Controllers\Api\BuyUserApiController::class, 'getDashboardCount']);

Route::POST('v1/submit_buy_form', [App\Http\Controllers\Api\BuyUserApiController::class, 'addupdatebuyuser']);
Route::POST('v1/verify_email', [App\Http\Controllers\Api\BuyUserApiController::class, 'verifyemail']);
Route::POST('v1/resend_otp', [App\Http\Controllers\Api\BuyUserApiController::class, 'resendotp']);

Route::POST('v1/submit_sell_form', [App\Http\Controllers\Api\BuyUserApiController::class, 'addupdateselluser']);
Route::POST('v1/submit_buy_sell_form', [App\Http\Controllers\Api\BuyUserApiController::class, 'addupdatebuyselluser']);
Route::POST('v1/submit_agent_form', [App\Http\Controllers\Api\BuyUserApiController::class, 'addupdateagentuser']);
Route::POST('v1/admin_login', [App\Http\Controllers\Api\BuyUserApiController::class, 'adminlogin']);
Route::POST('v1/update_agent', [App\Http\Controllers\Api\BuyUserApiController::class, 'updateagent']);
Route::POST('v1/addupdate_settings', [App\Http\Controllers\Api\BuyUserApiController::class, 'addupdatesettings']);
Route::GET('v1/get_settings', [App\Http\Controllers\Api\BuyUserApiController::class, 'getsettings']);
Route::DELETE('v1/delete_userbyid', [App\Http\Controllers\Api\BuyUserApiController::class, 'deleteuserbyid']);
Route::POST('v1/exportBuySell', [App\Http\Controllers\Api\BuyUserApiController::class, 'exportBuySell']);

Route::POST('v1/updateStatus', [App\Http\Controllers\Api\BuyUserApiController::class, 'updateStatus']);

Route::POST('v1/uploadimage', [App\Http\Controllers\Api\BuyUserApiController::class, 'uploadimage']);
Route::GET('v1/getfilterCount', [App\Http\Controllers\Api\BuyUserApiController::class, 'getfilterCount']);

Route::GET('v1/getPropertylist', [App\Http\Controllers\Api\PropertyApiController::class, 'getPropertylist']);



Route::POST('v1/addupdateproperty', [App\Http\Controllers\Api\PropertyApiController::class, 'addupdateproperty']);
Route::POST('v1/propertyenquiry', [App\Http\Controllers\Api\PropertyApiController::class, 'propertyenquiry']);

Route::GET('v1/getactivePropertylist', [App\Http\Controllers\Api\PropertyApiController::class, 'getactivePropertylist']);

Route::GET('v1/getactivePropertybyid', [App\Http\Controllers\Api\PropertyApiController::class, 'getactivePropertybyid']);

Route::GET('v1/getPropertyenquirylist', [App\Http\Controllers\Api\PropertyApiController::class, 'getPropertyenquirylist']);






//end LevelapiController

