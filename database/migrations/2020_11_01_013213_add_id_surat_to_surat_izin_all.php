<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdSuratToSuratIzinAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $set = ['tb_izin_sakit', 'tb_kegiatan', 'tb_keluar_kampus', 'tb_orangtua', 'tb_pemakaman',
                    'tb_pernikahan_saudara', 'tb_pesiar', 'tb_training', 
                    'tb_tugas'];
        foreach ($set as $key => $value) {
            Schema::table($value, function (Blueprint $table) {
                $table->integer('id_surat')->nullable();
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
        $set = ['tb_izin_sakit', 'tb_kegiatan', 'tb_keluar_kampus', 'tb_orangtua', 'tb_pemakaman', 
                    'tb_pengaduan', 'tb_pengasuhan_daring', 'tb_penghargaan', 
                    'tb_pernikahan_saudara', 'tb_pesiar', 'tb_training', 
                    'tb_tugas'];

        foreach ($set as $key => $value) {
            Schema::table($value, function (Blueprint $table) {
                $table->dropColumn(['id_surat']);
            });
        }
        
    }
}
