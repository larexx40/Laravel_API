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
        Schema::create('send_grid_api_details', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->default('');
            $table->string('emailfrom', 100)->default('');
            $table->integer('status')->default(0);
            $table->string('apikey', 200)->default('');
            $table->string('secretid', 200)->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('send_grid_api_details');
    }
};
