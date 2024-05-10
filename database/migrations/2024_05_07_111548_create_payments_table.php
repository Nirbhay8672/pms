<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('website_id')->nullable();
            $table->date('payment_date');
            $table->time('payment_time');
            $table->bigInteger('amount');
            $table->string('status')->nullable();
            $table->unsignedBigInteger('package_type_id')->nullable();
            $table->date('last_try')->nullable();
            $table->date('last_success')->nullable();
            $table->timestamps();

            $table->foreign('package_type_id')->references('id')->on('package_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_package_type_id_foreign');
        });
        Schema::dropIfExists('payments');
    }
};