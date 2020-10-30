<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SuratTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_template', function (Blueprint $table) {
            $table->id();
            $table->longText('header')->nullable();
            $table->longText('body')->nullable();
            $table->longText('footer')->nullable();
            $table->integer('id_category');
            $table->integer('user_created')->nullable();
            $table->integer('user_updated')->nullable();
            $table->integer('user_deleted')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('surat_template');
    }
}
