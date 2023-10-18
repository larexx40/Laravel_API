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
            $table->dropColumn('name');  
            $table->string('fname')->default('')->change();
            $table->string('lname')->default('')->change();
            $table->string('username')->default('')->change();
            $table->string('phoneno')->default('')->change();
            $table->string('pin')->default('')->change();
            $table->string('profile_pic')->default('')->change();
            $table->string('userpubkey')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function ($table) {
            $table->dropColumn('name');
            $table->dropColumn('fname');
            $table->dropColumn('lname');
            $table->dropColumn('username');
            $table->dropColumn('phoneno');
            $table->dropColumn('pin');
            $table->dropColumn('profile_pic');
            $table->dropColumn('userpubkey');
        });
    }
};
