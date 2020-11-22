<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartToTbHukdis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_hukdis', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_hukdis', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
}
