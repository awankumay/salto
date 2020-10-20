<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeluargaAsuhBodyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keluarga_asuh_body', function (Blueprint $table) {
            $table->id();
            $table->integer('id_keluarga_asuh_header');
            $table->integer('id_taruna');
            $table->integer('id_grade')->nullable();
            $table->integer('author');
            $table->integer('user_updated')->nullable();
            $table->softDeletes('deleted_at');
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
        Schema::dropIfExists('keluarga_asuh_body');
    }
}
