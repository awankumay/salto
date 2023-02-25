<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisposisiToSuratHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surat_header', function (Blueprint $table) {
            $table->timestamp('date_disposisi')->nullable();
            $table->integer('user_disposisi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surat_header', function (Blueprint $table) {
            $table->dropColumn(['date_dispoisi', 'user_disposisi']);
        });
    }
}
