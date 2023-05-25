<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});





require __DIR__.'/auth.php';

//Authenticated Routes comes Here;
Route::middleware(['auth'])->group(function () {
    Route::get('2faunthentication', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'twoFactor'])->name('2fa');
    Route::post('2faunthentication', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'twoFactorVerify'])->name('2fa.verify');
    Route::post('2faunthentication/resend', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'twoFactorResend'])->name('2fa.resend');

    //All 2fa Routes and Verified
    Route::middleware(['verified','2fa'])->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\UserController::class, 'dashboard'])->name('user.dashboard');
        Route::get('invoice',[\App\Http\Controllers\InvoiceController::class,'index'])->name('invoice.listing');
        Route::get('transactions',\App\Http\Livewire\TransactionsPage::class)->name('transactions');
        Route::get('payment_links', \App\Http\Livewire\PaymentLinks::class)->name('payment_links');
        Route::get('download/{filename}/{path}',[\App\Http\Controllers\DownloadsController::class, 'download'])->name("download_report");


        //all admin route here;
       Route::middleware(['admin'])->group(function () {
           Route::get('admin_dboard',[\App\Http\Controllers\AdminController::class,'index'])->name('admin.dashboard');
           Route::get('payment_gateways', \App\Http\Livewire\PaymentGateway::class )->name('paymentgateay.index');
           Route::post('payment_gateways', [\App\Http\Controllers\PaymentController::class,'store'] )->name('paymentgateway.add');
           Route::patch('payment_gateways', [\App\Http\Controllers\PaymentController::class,'update'] )->name('paymentgateway.edit');
           Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('admin.users');
           Route::get('webhooks',[\App\Http\Controllers\WebhookController::class,'index'])->name('webhooks');
           Route::get("requestlogs",\App\Http\Livewire\RequestLogs::class)->name('request-logs');
           Route::get('requery_tool', \App\Http\Livewire\RequeryTool::class )->name('requery-tool');
           Route::get('payment_resolution',\App\Http\Livewire\PaymentResolution::class)->name('payment_resolution');

       });


    });

});




Route::get('payment/process/{id}',[\App\Http\Controllers\PaymentController::class, "paymentPage"])->name('payment-page');
Route::get('payment/card/validate/{id}',[\App\Http\Controllers\PaymentController::class, "validateCardPayment"]);
Route::get('payment/receipt/{id}',[\App\Http\Controllers\PaymentController::class,'receipt'])->name('receipt');

