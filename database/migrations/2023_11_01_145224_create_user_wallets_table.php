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
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('userid')->default('');
            $table->string('currencytag')->default('');
            $table->string('wallettrackid')->default('');
            $table->decimal('walletbal', 11,2)->default(0.00);
            $table->decimal('walletpendbal', 11,2)->default(0.00);
            $table->decimal('walletescrowbal', 11,2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};
