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
        

        Schema::table('user_tokens', function (Blueprint $table) {
            // Add the 'token' column after the 'identity_type' column
            $table->string('token')->unique()->default('')->comment('4 digit otp')->after('identity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('user_tokens', function ($table) {
            $table->dropColumn('token');
        });

    }
};
