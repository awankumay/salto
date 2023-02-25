<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToTbPenghargaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_penghargaan', function (Blueprint $table) {
            $table->integer('user_disposisi')->nullable();
            $table->integer('status_disposisi')->nullable();
            $table->timestamp('date_disposisi')->nullable();
            $table->longText('reason_disposisi')->nullable();
            $table->integer('user_approve_level_1')->nullable();
            $table->integer('status_level_1')->nullable();
            $table->timestamp('date_approve_level_1')->nullable();
            $table->longText('reason_level_1')->nullable();
            $table->string('photo')->nullable();
            $table->integer('grade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_penghargaan', function (Blueprint $table) {
            $table->dropColumn(['user_disposisi', 'status_disposisi', 'date_disposisi', 
                                'reason_disposisi', 'user_approve_level_1', 'status_level_1', 
                                'date_approve_level_1', 'reason_level_1', 'photo', 'grade']);
        });
    }
}
