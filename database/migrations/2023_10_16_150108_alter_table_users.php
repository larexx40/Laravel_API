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
            $table->string('userid', 20)->after('password');
            $table->string('fname', 50)->after('userid');
            $table->string('lname', 50)->after('fname');
            $table->string('username', 50)->after('lname');
            $table->string('phoneno', 50)->after('username');
            $table->string('pin', 200)->after('phoneno');
            $table->string('profile_pic', 200)->after('pin');
            $table->string('userpubkey', 200)->after('profile_pic');
        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function ($table) {
            $table->dropColumn('userid');
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
