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
        Schema::create('website_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id');
            $table->double('wp_status');
            $table->bigInteger('site_views');
            $table->double('speed_by_google');
            $table->double('accessibility_score');
            $table->double('seo_score');
            $table->double('security');
            $table->double('code_quality');
            $table->timestamps();

            $table->foreign('website_id')->references('id')->on('websites');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_details', function (Blueprint $table) {
            $table->dropForeign('website_details_website_id_foreign');
        });
        Schema::dropIfExists('website_details');
    }
};