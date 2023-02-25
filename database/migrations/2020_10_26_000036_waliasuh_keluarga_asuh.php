<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WaliasuhKeluargaAsuh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waliasuh_keluarga_asuh', function (Blueprint $table) {
            $table->id();
            $table->integer('keluarga_asuh_id');
            $table->integer('waliasuh_id');
            $table->integer('user_created');
            $table->integer('user_updated')->nullable();
            $table->integer('user_deleted')->nullable();
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
        Schema::dropIfExists('waliasuh_keluarga_asuh');
    }
}
