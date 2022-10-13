<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDynamicAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number');
            $table->string('account_name');
            $table->string('initiationTranRef');
            $table->string('invoice_no');
            $table->foreign('invoice_no')->references('invoice_no')->on('invoices')->cascadeOnDelete();
            $table->boolean('status');
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
        Schema::dropIfExists('dynamic_accounts');
    }
}
