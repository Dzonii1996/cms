<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('main_page_id')->nullable()->constrained('page_relations');
                $table->string('lang');
                $table->string('title');
                $table->string('slug');
                $table->text('content')->nullable();
                $table->foreignId('featured_image_id')->nullable()->constrained('media');
                $table->integer('author_id');
                $table->foreignId('status_id')->constrained('statuses');
                $table->timestamps();
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
        Schema::dropIfExists('pages');
    }
}
