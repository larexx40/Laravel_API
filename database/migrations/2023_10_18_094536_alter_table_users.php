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
            $table->string('email', 100)->default('')->change();
            $table->string('userid', 50)->default('')->change();
            $table->string('userpubkey', 100)->default('')->change();
            $table->string('password', 200)->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function ($table) {
            $table->dropColumn('email');
            $table->dropColumn('userid');
            $table->dropColumn('userpubkey');
            $table->dropColumn('password');
            
        });
    }
};
