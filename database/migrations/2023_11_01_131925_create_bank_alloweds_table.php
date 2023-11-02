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

        Schema::create('bank_alloweds', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('image_link')->default('');
            $table->string('sysbankcode')->default('');
            $table->string('paystackbankcode')->default('');
            $table->string('monifybankcode')->default('');
            $table->string('shbankcodes')->default('');
            $table->integer('status')->default(0)->comment("0-Incactive, 1-Active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_alloweds');
    }
};
