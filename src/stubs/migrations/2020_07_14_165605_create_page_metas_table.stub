<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_metas', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code');
            $table->string('title')->nullable();
            $table->string('type');
            $table->longText('content')->nullable();

            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('page_metas');
    }
}
