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
        //`name`, `iosversion`, `androidversion`, `webversion`, `activesmssystem`, `activemailsystem`, `emailfrom`, `baseurl`, `location`, `appshortdetail`, `activepaysystem`, `activebanksystem`, `supportemail`, `appimgurl`, `referalpointforusers`, `created_at`, `updated_at`, `activate_referral_bonus`
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('iosversion')->default('');
            $table->string('androidversion')->default('');
            $table->string('webversion')->default('');
            $table->integer('activesmssystem')->default(0);
            $table->integer('activemailsystem')->default(0);
            $table->string('emailfrom')->default('');
            $table->string('baseurl')->default('');
            $table->string('location')->default('');
            $table->string('appshortdetail')->default('');
            $table->integer('activepaysystem')->default(1);
            $table->integer('activebanksystem')->default(1);
            $table->string('supportemail')->default('');
            $table->string('appimgurl')->default('');
            $table->integer('referalpointforusers')->default(0);
            $table->integer('activate_referral_bonus')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
