<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeluargaAsuhHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keluarga_asuh_header', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('end_date');
            $table->integer('id_wali_asuh')->nullable();
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('keluarga_asuh_header');
    }
}
