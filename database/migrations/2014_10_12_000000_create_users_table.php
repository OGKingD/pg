<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedBigInteger('type')->default(5);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('business_name')->nullable();
            $table->text('support_email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->integer('status')->default(0);
            $table->string('ip_address', 32)->nullable();
            $table->string('last_login', 32)->nullable();
            $table->integer('kyc_level')->default(0);
            $table->tinyInteger('twofactor_auth')->nullable();
            $table->tinyInteger('email_notify')->nullable();
            $table->string('googlefa_secret', 32)->nullable();
            $table->integer('business_level')->default(1);
            $table->json('gateways')->nullable();
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
        Schema::dropIfExists('users');
    }
}
