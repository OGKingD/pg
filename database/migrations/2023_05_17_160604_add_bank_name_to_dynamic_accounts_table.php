<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('dynamic_accounts', function (Blueprint $table) {
            $table->string('bank_name')->after('account_name');
        });
    }

    public function down()
    {
        Schema::table('dynamic_accounts', function (Blueprint $table) {
            //
            $table->dropColumn('bank_name');
        });
    }
};
