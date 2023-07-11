<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('payment_provider_message');
            $table->string('redirect_url')->nullable()->after('details');
            $table->string('merchant_transaction_charge')->nullable()->default(0)->after('fee');
            $table->string('service_charge')->nullable()->default(0)->after('fee');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
            $table->dropColumn('provider');
            $table->dropColumn('merchant_transaction_charge');
            $table->dropColumn('service_charge');
            $table->dropColumn('redirect_url');
        });
    }
};
