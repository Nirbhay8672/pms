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
            $table->string('wp_username')->nullable();
            $table->string('wp_password')->nullable();
            $table->string('otech_username');
            $table->string('otech_password');
            $table->string('licence_key')->unique();
            $table->string('plugin_version')->nullable();
            $table->integer('plugin_is_active')->nullable();
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
