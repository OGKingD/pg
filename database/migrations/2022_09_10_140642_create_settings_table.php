<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title', 200)->nullable();
            $table->string('site_name', 200)->nullable();
            $table->text('site_desc')->nullable();
            $table->string('email', 128)->nullable();
            $table->string('support_email')->nullable();
            $table->string('mobile', 128)->nullable();
            $table->integer('balance_reg')->nullable();
            $table->integer('email_notify')->nullable();
            $table->integer('sms_notify')->nullable();
            $table->integer('kyc')->nullable();
            $table->integer('email_verification')->nullable();
            $table->integer('sms_verification')->nullable();
            $table->integer('registration')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
