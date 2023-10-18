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
            $table->string('sys_bank_id');        
            $table->integer('is_default')->default(0)->change();
            $table->integer('status')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('user_bank_accounts', function ($table) {
            $table->dropColumn('sys_bank_id');
            $table->dropColumn('is_default');
            $table->dropColumn('status');
        });
    }
};
