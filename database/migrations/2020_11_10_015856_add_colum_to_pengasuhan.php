<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumToPengasuhan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_pengasuhan_daring', function (Blueprint $table) {
            $table->longText('reason')->nullable();
            $table->integer('keluarga_asuh_id')->nullable();
            $table->timestamp('date_approve')->nullable();
            $table->integer('user_approve')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_pengasuhan_daring', function (Blueprint $table) {
            $table->dropColumn(['reason', 'keluarga_asuh_id', 'date_approve', 'user_approve']);
        });
    }
}
