<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('webhook_pushes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');
            $table->foreignId('transaction_id');
            $table->integer('count')->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->boolean('status')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('webhook_pushes');
    }
};
