<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimesToSuratHeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surat_header', function (Blueprint $table) {
            $table->timestamp('grade')->nullable();
            $table->timestamp('start')->nullable();
            $table->integer('end')->nullable();
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
            $table->dropColumn(['grade', 'start', 'end']);
        });
    }
}
