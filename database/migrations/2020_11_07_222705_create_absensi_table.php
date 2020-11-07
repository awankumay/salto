<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absensi_taruna', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->timestamp('clock_in');
            $table->timestamp('clock_out')->nullable();
            $table->string('file_clock_in')->nullable();
            $table->string('file_clock_out')->nullable();
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
        Schema::dropIfExists('absensi_taruna');
    }
}
