<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transactions', static function (Blueprint $table) {
            $table->string('merchant_service_charge')->default(0);
            $table->decimal('merchant_service_charge_amount')->default(0);
            $table->string('customer_service_charge')->default(0);
            $table->decimal('customer_service_charge_amount')->default(0);
            $table->decimal('stamp_duty')->default(0);
        });
    }

    public function down()
    {
        Schema::table('transactions', static function (Blueprint $table) {
            //
            $table->dropColumn('merchant_service_charge');
            $table->dropColumn('merchant_service_charge_amount');
            $table->dropColumn('customer_service_charge');
            $table->dropColumn('customer_service_charge_amount');
            $table->dropColumn('stamp_duty');
        });
    }
};
