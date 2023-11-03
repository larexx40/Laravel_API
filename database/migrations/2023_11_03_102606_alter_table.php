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
        //
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('walletid')->default('');

            // Define foreign key constraint
            $table->foreign('walletid')->references('wallettrackid')->on('user_wallets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['walletid']);
        });
    }
};
