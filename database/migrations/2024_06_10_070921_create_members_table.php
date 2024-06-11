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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email');
            $table->integer('user_status')->default(1);
            $table->bigInteger('phone_number');
            $table->string('website_name');
            $table->string('website_link')->unique();
            $table->string('wp_username');
            $table->string('wp_password');
            $table->string('business_code')->unique();
            $table->string('custom_key')->unique();
            $table->string('plugin_version')->nullable();
            $table->integer('send_update')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
