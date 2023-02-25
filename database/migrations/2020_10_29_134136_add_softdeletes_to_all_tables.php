<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftdeletesToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $set = ['tb_hukdis', 'tb_izin_sakit', 'tb_kegiatan', 'tb_keluar_kampus', 'tb_orangtua', 'tb_pemakaman', 
                    'tb_pengaduan', 'tb_pengasuhan_daring', 'tb_penghargaan', 
                    'tb_pernikahan_saudara', 'tb_pesiar', 'tb_suket', 'tb_training', 
                    'tb_tugas', 'tb_wbs'];
        foreach ($set as $key => $value) {
            Schema::table($value, function (Blueprint $table) {
                $table->timestamps();
                $table->integer('id_user')->nullable();
                $table->integer('user_created')->nullable();
                $table->integer('user_updated')->nullable();
                $table->integer('user_deleted')->nullable();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $set = ['tb_hukdis', 'tb_izin_sakit', 'tb_kegiatan', 'tb_keluar_kampus', 'tb_orangtua', 'tb_pemakaman', 
                    'tb_pengaduan', 'tb_pengasuhan_daring', 'tb_penghargaan', 
                    'tb_pernikahan_saudara', 'tb_pesiar', 'tb_suket', 'tb_training', 
                    'tb_tugas', 'tb_wbs'];

        foreach ($set as $key => $value) {
            Schema::table($value, function (Blueprint $table) {
                $table->dropColumn(['user_deleted', 'id_user', 'user_created', 'user_deleted', 'created_at', 'updated_at', 'deleted_at']);
            });
        }
        
    }
}
