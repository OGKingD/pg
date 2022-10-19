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



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth','verified'])->name('dashboard');

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


        //all admin route here;
       Route::middleware(['admin'])->group(function () {
           Route::get('admin_dboard',[\App\Http\Controllers\AdminController::class,'index'])->name('admin.dashboard');
           Route::get('payment_gateways', \App\Http\Livewire\PaymentGateway::class )->name('paymentgateay.index');
           Route::post('payment_gateways', [\App\Http\Controllers\PaymentController::class,'store'] )->name('paymentgateway.add');
           Route::patch('payment_gateways', [\App\Http\Controllers\PaymentController::class,'update'] )->name('paymentgateway.edit');
           Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('admin.users');
       });


    });

});




Route::get('test', function () {
    return view('test');
});
Route::get('payment/process/{id}',[\App\Http\Controllers\PaymentController::class, "paymentPage"]);
Route::get('payment/card/validate/{id}',[\App\Http\Controllers\PaymentController::class, "validateCardPayment"]);
Route::get('payment/receipt/{id}',[\App\Http\Controllers\PaymentController::class,'receipt'])->name('receipt');

