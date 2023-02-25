<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisposisiReasonToSuratHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surat_header', function (Blueprint $table) {
            $table->integer('status_disposisi')->nullable();
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
        Schema::table('surat_header', function (Blueprint $table) {
            $table->dropColumn(['status_disposisi', 'reason_disposisi']);
        });
    }
}
