<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
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

Route::middleware(['terminate'])->group(function () {
    Route::middleware(['api'])->group(function (){
        Route::get('welcome',function (Request $request){
            $inspirationalText = inspirationalText();
            return [ "message" => "Hello 👋  {$request->user()->first_name} : {$inspirationalText['quote']} -- {$inspirationalText['author']}", 'success' => true,];

        });
        //Payment Request Routes!
        Route::prefix('payments')->group(function () {
            Route::post('create',[PaymentController::class,"createPaymentRequest"]);
            Route::get('validate', [PaymentController::class, 'details']);
        });
    });


    //Unprotected Routes: Ideally webhook Routes
    Route::prefix("webhook")->group(function (){
        Route::post('flutterwave',[WebhookController::class,'flutterwave']);
        Route::post('providus',[WebhookController::class,'providusSettlement']);
    });
});
