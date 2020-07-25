<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTable extends Migration
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
            $table->string('title');
            $table->string('slug');
            $table->string('meta_title');
            $table->string('meta_description');
            $table->string('excerpt');
            $table->integer('post_categories_id');
            $table->longText('tags')->nullable();
            $table->longText('content');
            $table->string('photo')->nullable();
            $table->integer('headline');
            $table->integer('status');
            $table->bigInteger('author');
            $table->string('user_created');
            $table->string('user_updated')->nullable();
            $table->timestamp('date_published')->nullable();
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

        Schema::dropIfExists('posts');

    }
}
