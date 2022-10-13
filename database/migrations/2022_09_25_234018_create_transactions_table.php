<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('transaction_ref')->unique();
            $table->string('invoice_no')->unique()->nullable();
            $table->foreign('invoice_no')->references('invoice_no')->on('invoices')->cascadeOnDelete();
            $table->string('merchant_transaction_ref')->unique();
            $table->string('flutterwave_ref')->unique()->nullable();
            $table->string('remita_ref')->unique()->nullable();
            $table->string('bank_transfer_ref')->unique()->nullable();
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->foreign('gateway_id')->references('id')->on('gateways');
            $table->string('type')->nullable();
            $table->decimal('amount',10)->default(0);
            $table->decimal('fee',10)->default(0);
            $table->decimal('total',10)->default(0);
            $table->string('description')->nullable();
            $table->enum('status',['pending','failed','successful']);
            $table->enum('flag',['credit','debit']);
            $table->string("payment_provider_message")->nullable();
            $table->json("details")->nullable();
            $table->string('currency', 3)->default('NGN');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
