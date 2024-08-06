<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('linked_merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references("id")->on('users');
            $table->foreignId('merchant_id')->references("id")->on('users');
            $table->boolean('status')->default(false);
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('linked_merchants');
    }
};
