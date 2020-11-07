<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatlongToAbsensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absensi_taruna', function (Blueprint $table) {
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absensi_taruna', function (Blueprint $table) {
            $table->dropColumn(['lat', 'long']);
        });
    }
}
