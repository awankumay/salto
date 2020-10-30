<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToSuratHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surat_header', function (Blueprint $table) {
            $table->integer('status_level_1')->nullable();
            $table->integer('status_level_2')->nullable();
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
            $table->dropColumn(['status_level_1', 'status_level_2']);
        });
    }
}
