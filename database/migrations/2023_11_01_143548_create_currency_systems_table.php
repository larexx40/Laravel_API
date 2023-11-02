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
        Schema::create('currency_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('nametag')->default('');
            $table->string('sign')->default('');
            $table->integer('currency_status')->default(0);
            $table->string('currencytag')->default('');
            $table->string('sidebarname')->default('');
            $table->string('imglink')->default('');
            $table->integer('activatesend')->default(0);
            $table->integer('activatereceive')->default(0);
            $table->string('maxsendamtauto')->default('');
            $table->integer('defaultforusers')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_systems');
    }
};
