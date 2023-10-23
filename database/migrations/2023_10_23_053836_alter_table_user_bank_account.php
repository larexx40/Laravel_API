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
        Schema::table('user_bank_accounts', function ($table) {  
            $table->string('user_id', 50)->default('')->change(); 
            $table->foreign('user_id')->references('userid')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
        Schema::table('user_bank_accounts', function ($table) {  
            $table->dropColumn('user_id');
        });
    }
};
