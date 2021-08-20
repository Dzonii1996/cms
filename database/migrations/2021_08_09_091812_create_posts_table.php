<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('lang');
            $table->foreignId('main_post_id')->nullable()->constrained('post_relations');
            $table->string('title');
            $table->string('slug');
            $table->text('content')->nullable();
            $table->timestamps();
            $table->foreignId('featured_image_id')->nullable()->constrained('media');
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
