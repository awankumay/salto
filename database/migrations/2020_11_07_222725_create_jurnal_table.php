<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurnalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurnal_taruna', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->date('tanggal');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->longText('kegiatan');
            $table->integer('status');
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
        Schema::dropIfExists('jurnal_taruna');
    }
}
