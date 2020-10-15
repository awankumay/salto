<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $permissions = [
            
            'kategori-surat-izin-list',
            'kategori-surat-izin-create',
            'kategori-surat-izin-edit',
            'kategori-surat-izin-delete',
     
            'kategori-berita-list',
            'kategori-berita-create',
            'kategori-berita-edit',
            'kategori-berita-delete',

            'surat-izin-orang-tua-sakit-list',
            'surat-izin-orang-tua-sakit-create',
            'surat-izin-orang-tua-sakit-edit',
            'surat-izin-orang-tua-sakit-delete',
            'surat-izin-orang-tua-sakit-approve',

            'surat-izin-orang-tua-meninggal-list',
            'surat-izin-orang-tua-meninggal-create',
            'surat-izin-orang-tua-meninggal-edit',
            'surat-izin-orang-tua-meninggal-delete',
            'surat-izin-orang-tua-meninggal-approve',

            'surat-izin-pernikahan-saudara-list',
            'surat-izin-pernikahan-saudara-create',
            'surat-izin-pernikahan-saudara-edit',
            'surat-izin-pernikahan-saudara-delete',
            'surat-izin-pernikahan-saudara-approve',

            'surat-izin-kegiatan-pesiar-list',
            'surat-izin-kegiatan-pesiar-create',
            'surat-izin-kegiatan-pesiar-edit',
            'surat-izin-kegiatan-pesiar-delete',
            'surat-izin-kegiatan-pesiar-approve',

            'surat-izin-rawat-inap-list',
            'surat-izin-rawat-inap-create',
            'surat-izin-rawat-inap-edit',
            'surat-izin-rawat-inap-delete',
            'surat-izin-rawat-inap-approve',

            'surat-izin-training-list',
            'surat-izin-training-create',
            'surat-izin-training-edit',
            'surat-izin-training-delete',
            'surat-izin-training-approve',

            'surat-izin-keluar-kampus-list',
            'surat-izin-keluar-kampus-create',
            'surat-izin-keluar-kampus-edit',
            'surat-izin-keluar-kampus-delete',
            'surat-izin-keluar-kampus-approve',

            'surat-izin-kegiatan-dalam-list',
            'surat-izin-kegiatan-dalam-create',
            'surat-izin-kegiatan-dalam-edit',
            'surat-izin-kegiatan-dalam-delete',
            'surat-izin-kegiatan-dalam-approve',

            'surat-tugas-list',
            'surat-tugas-create',
            'surat-tugas-edit',
            'surat-tugas-delete',
            'surat-tugas-approve',

            'prestasi-taruna-list',
            'prestasi-taruna-create',
            'prestasi-taruna-edit',
            'prestasi-taruna-delete',
            'prestasi-taruna-approve',

            'hukuman-dinas-list',
            'hukuman-dinas-create',
            'hukuman-dinas-edit',
            'hukuman-dinas-delete',
            'hukuman-dinas-approve',

            'surat-keterangan-list',
            'surat-keterangan-create',
            'surat-keterangan-edit',
            'surat-keterangan-delete',
            'surat-keterangan-approve',

            'pengasuhan-daring-list',
            'pengasuhan-daring-create',
            'pengasuhan-daring-edit',
            'pengasuhan-daring-delete',
            'pengasuhan-daring-approve',
             
            'absensi-list',
            'absensi-create',
            'absensi-edit',
            'absensi-delete',
            'absensi-delete',

            'jurnal-harian-list',
            'jurnal-harian-create',
            'jurnal-harian-edit',
            'jurnal-harian-delete',
            'jurnal-harian-approve',

            'data-kegiatan-jurnal-harian-list',
            'data-kegiatan-jurnal-harian-create',
            'data-kegiatan-jurnal-harian-edit',
            'data-kegiatan-jurnal-harian-delete',

            'data-keluarga-asuh-list',
            'data-keluarga-asuh-create',
            'data-keluarga-asuh-edit',
            'data-keluarga-asuh-delete',

            'data-pembina-keluarga-asuh-list',
            'data-pembina-keluarga-asuh-create',
            'data-pembina-keluarga-asuh-edit',
            'data-pembina-keluarga-asuh-delete',

            'pengaduan-wbs-list',
            'pengaduan-wbs-create',
            'pengaduan-wbs-edit',
            'pengaduan-wbs-delete',

            'berita-list',
            'berita-create',
            'berita-edit',
            'berita-delete',

            'banner-list',
            'banner-create',
            'banner-edit',
            'banner-delete',
            
            'profil-edit',

            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

         ];

         foreach ($permissions as $permission) {
              Permission::updateOrCreate(['name' => $permission]);
         }
         $role = Role::updateOrCreate(['name' => 'Super Admin']);
         $permissions = Permission::pluck('id','id')->all();
         $role->syncPermissions($permissions);
    }
}
