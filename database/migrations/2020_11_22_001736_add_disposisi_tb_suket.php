<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisposisiTbSuket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_suket', function (Blueprint $table) {
            $table->integer('user_disposisi')->nullable();
            $table->integer('status_disposisi')->nullable();
            $table->timestamp('date_disposisi')->nullable();
            $table->longText('reason_disposisi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_suket', function (Blueprint $table) {
            $table->dropColumn(['user_disposisi', 'status_disposisi', 'date_disposisi', 'reason_disposisi']);
        });
    }
}
