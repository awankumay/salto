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
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('excerpt');
            $table->integer('post_categories_id');
            $table->longText('content');
            $table->string('photo')->nullable();
            $table->integer('headline');
            $table->integer('status');
            $table->integer('user_created');
            $table->integer('user_updated')->nullable();
            $table->integer('user_deleted')->nullable();
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
