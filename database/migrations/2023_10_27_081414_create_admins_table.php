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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->default('');
            $table->string('username', 50)->default('')->unique();
            $table->string('adminid', 50)->default('')->unique();
            $table->string('email', 100)->unique();
            $table->string('password', 300);
            $table->string('phoneno')->default('')->unique();
            $table->integer('status')->default(0);
            $table->string('adminlevel', 50)->default('');
            $table->string('adminpubkey', 300)->default('')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
