<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPengaduanToTbWbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_wbs', function (Blueprint $table) {
            $table->longText('pengaduan')->nullable();
            $table->longText('follow_up')->nullable();
            $table->timestamp('date_follow_up')->nullable();
            $table->integer('user_follow_up')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_wbs', function (Blueprint $table) {
            $table->dropColumn(['pengaduan', 'follow_up', 'user_follow_up', 'date_follow_up']);
        });
    }
}
