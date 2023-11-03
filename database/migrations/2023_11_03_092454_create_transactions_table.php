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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transactionid')->default('')->unique();
            $table->integer('transaction_type')->default(0)->comment('1-Fund, 2-Swap Usdt-Naira, 3-Sendt, 4-WIthdraw');
            $table->decimal('amount_crypto', 18, 8)->default(0.0); //18,8 for crypto
            $table->decimal('amount_fiat', 10, 2)->default(0.0);// 10 digits, 2 decimal places, default to 0.0
            $table->integer('status')->default(0)->comment('0-Pending, 1-Success, 2-Failed');
            $table->string('userid')->default('');
            $table->string('charges')->default('');
            $table->string('userbankid')->default('');
            // $table->string('walletid')->default('');

            // Define foreign key constraint
            $table->foreign('userid')->references('userid')->on('users')->onDelete('cascade');
            // $table->foreign('walletid')->references('wallettrackid')->on('user_wallets')->onDelete('cascade');
            $table->foreign('userbankid')->references('bankid')->on('user_bank_accounts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
