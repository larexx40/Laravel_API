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
        Schema::table('users', function ($table) {  
            $table->integer('is_email_verified')->default(0);
            $table->integer('is_phone_verified')->default(0);
            $table->string('refcode')->default('');
            $table->string('refby')->default('');
            $table->string('fcm')->default('');
            $table->string('dob')->default('');
            $table->string('sex')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function ($table) {
            $table->dropColumn('is_email_verified');
            $table->dropColumn('is_phone_verified');
            $table->dropColumn('refcode');
            $table->dropColumn('refby');
            $table->dropColumn('fcm');
            $table->dropColumn('dob');
            $table->dropColumn('sex');
            
        });
    }
};
