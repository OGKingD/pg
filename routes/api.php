<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use App\Lib\Services\Providus;
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
            return ['status' => true, "message" => "Hello 👋  {$request->user()->first_name} : {$inspirationalText['quote']} -- {$inspirationalText['author']}", ];

        });
        //Payment Request Routes!
        Route::prefix('payments')->group(function () {
            Route::post('create',[PaymentController::class,"createPaymentRequest"]);
            Route::post('update',[PaymentController::class,"updatePaymentRequest"]);
            Route::get('validate', [PaymentController::class, 'details']);
            Route::get('details/{id}',[\App\Http\Controllers\CashAtBankController::class,'show']);
            Route::post('pay',[\App\Http\Controllers\CashAtBankController::class, 'store']);
            Route::get('channels',[PaymentController::class,'getPaymentChannels']);
            Route::get('get_charge',[PaymentController::class,'computeChargeAndTotal']);
            Route::prefix("process")->group(function (){
                Route::post('bank_transfer',[PaymentController::class,'processBankTransfer']);
                Route::prefix('card')->group(function (){
                    Route::post('',[PaymentController::class,'processCardTransaction']);
                    Route::post('authorization_pin',[PaymentController::class,'authorizeCardWithPin']);
                    Route::post('authorization_otp',[PaymentController::class,'authorizeCardWithOtp']);
                    Route::post('authorization_avs',[PaymentController::class,'authorizeCardWithAvs']);
                });
            });
            if (strtoupper(config('app.env')) != "PRODUCTION"){
                Route::post('consumate',[PaymentController::class,'consumatePayment']);
            }
        });
    });


    //Unprotected Routes: Ideally webhook Routes
    Route::prefix("webhook")->group(function (){
        Route::post('flutterwave',[WebhookController::class,'flutterwave'])->name('webhook.flutterwave');
        Route::post('flutterwavepercent',[WebhookController::class,'flwavePercent'])->name('webhook.flutterwave.percent');
        Route::post('providus',[WebhookController::class,'providusSettlement']);
        Route::any('remita',[WebhookController::class,'remitaSettlement'])->name('webhook.remita-settlement');
        Route::post('ninepsbvirtual',[WebhookController::class,'ninePsbSettlement'])->name('webhook.nine-psb-settlement');
        Route::any('blusalt',[WebhookController::class,'blusalt'])->name('webhook.blusalt');
    });
});
