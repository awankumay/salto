<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuPersetujuanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_persetujuan', function (Blueprint $table) {
            $table->softDeletes('deleted_at');
            $table->timestamps();
            $table->integer('author')->null();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_persetujuan', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['author']);
        });
    }
}
