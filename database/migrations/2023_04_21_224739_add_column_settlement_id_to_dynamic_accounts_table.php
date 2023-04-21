<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('dynamic_accounts', function (Blueprint $table) {
            $table->string('settlement_id')->nullable();
            $table->string('session_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('dynamic_accounts', function (Blueprint $table) {
            //
        });
    }
};
