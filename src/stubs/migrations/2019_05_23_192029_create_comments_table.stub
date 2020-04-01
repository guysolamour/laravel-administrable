<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            $table->string('commenter_id')->nullable();
            $table->string('commenter_type')->nullable();
            $table->index(["commenter_id", "commenter_type"]);

            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            $table->string("commentable_type");
            $table->string("commentable_id");
            $table->index(["commentable_type", "commentable_id"]);

            $table->text('comment');

            $table->boolean('approved')->default(true);
            $table->boolean('reply_notification')->default(true);

            $table->foreignId('child_id')->nullable()->constrained('comments')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
