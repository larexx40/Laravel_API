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
       

        Schema::table('admins', function (Blueprint $table) {
            // Check if columns exist before modifying them
            if (Schema::hasColumn('admins', 'profile_updated')) {
                $table->integer('profile_updated')->default(0)->change();
            }

            if (Schema::hasColumn('admins', 'password_updated')) {
                $table->integer('password_updated')->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('admins', function ($table) {
            $table->dropColumn('profile_updated');
            $table->dropColumn('password_updated');
        });
    }
};
