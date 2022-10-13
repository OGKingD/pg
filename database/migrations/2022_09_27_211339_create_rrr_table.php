<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRrrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrrs', function (Blueprint $table) {
            $table->id();
            $table->string('rrr')->unique();
            $table->string('invoice_no');
            $table->string('transaction_ref')->nullable();
            $table->foreign('invoice_no')->references('invoice_no')->on('invoices')->cascadeOnDelete();
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
        Schema::dropIfExists('rrr');
    }
}
