<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoMetaTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_meta_tags', function (Blueprint $table) {
            $table->id();

            $table->string('page:title')->nullable();
            $table->string('page:canonical:url')->nullable();
            $table->string('page:author')->nullable();
            $table->string('page:meta:description')->nullable();
            $table->string('page:meta:keywords')->nullable();
            $table->string('og:locale')->nullable();
            $table->string('og:type')->default('article')->nullable();
            $table->string('og:title')->nullable();
            $table->string('og:description')->nullable();
            $table->string('og:url')->nullable();
            $table->string('og:image')->nullable();
            $table->string('twitter:type')->default('summary')->nullable();
            $table->string('twitter:title')->nullable();
            $table->string('twitter:image')->nullable();
            $table->string('twitter:description')->nullable();
            $table->string('robots:index')->nullable();
            $table->string('robots:follow')->nullable();

            $table->morphs('seoable');
            $table->longText('html')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_meta_tags');
    }
}
