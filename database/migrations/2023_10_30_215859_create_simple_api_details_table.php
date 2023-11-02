<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('simple_api_details', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id')->default('');
            $table->string('public_key')->default('');
            $table->string('secret_key')->default('');
            $table->integer('status')->default(0);
            $table->timestamps();
        });
        Schema::create('termi_api_details', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('sendfrom')->default('');
            $table->string('smschannel')->default('');
            $table->string('smstype')->default('');
            $table->string('apikey')->default('');
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simple_api_details');
        Schema::dropIfExists('termi_api_details');
    }
};
