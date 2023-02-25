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
          'absensi-list',
          'absensi-create',
          'absensi-edit',
          'absensi-delete',
          'absensi-delete',

          'banner-list',
          'banner-create',
          'banner-edit',
          'banner-delete',

          'berita-list',
          'berita-create',
          'berita-edit',
          'berita-delete',

          'data-keluarga-asuh-list',
          'data-keluarga-asuh-create',
          'data-keluarga-asuh-edit',
          'data-keluarga-asuh-delete',

          'data-pembina-keluarga-asuh-list',
          'data-pembina-keluarga-asuh-create',
          'data-pembina-keluarga-asuh-edit',
          'data-pembina-keluarga-asuh-delete',

          'data-waliasuh-keluarga-asuh-list',
          'data-waliasuh-keluarga-asuh-create',
          'data-waliasuh-keluarga-asuh-edit',
          'data-waliasuh-keluarga-asuh-delete',

          'data-orangtua-taruna-list',
          'data-orangtua-taruna-create',
          'data-orangtua-taruna-edit',
          'data-orangtua-taruna-delete',

          'hukuman-dinas-list',
          'hukuman-dinas-create',
          'hukuman-dinas-edit',
          'hukuman-dinas-delete',
          'hukuman-dinas-approve',

          'jurnal-harian-list',
          'jurnal-harian-create',
          'jurnal-harian-edit',
          'jurnal-harian-delete',
          'jurnal-harian-approve',

          'kategori-surat-izin-list',
          'kategori-surat-izin-create',
          'kategori-surat-izin-edit',
          'kategori-surat-izin-delete',

          'kategori-berita-list',
          'kategori-berita-create',
          'kategori-berita-edit',
          'kategori-berita-delete',

          'pengasuhan-daring-list',
          'pengasuhan-daring-create',
          'pengasuhan-daring-edit',
          'pengasuhan-daring-delete',
          'pengasuhan-daring-approve',

          'pengaduan-wbs-list',
          'pengaduan-wbs-create',
          'pengaduan-wbs-edit',
          'pengaduan-wbs-delete',

          'pengaduan-list',
          'pengaduan-create',
          'pengaduan-edit',
          'pengaduan-delete',

          'prestasi-taruna-list',
          'prestasi-taruna-create',
          'prestasi-taruna-edit',
          'prestasi-taruna-delete',
          'prestasi-taruna-approve',
          
          'profil-edit',

          'role-list',
          'role-create',
          'role-edit',
          'role-delete',
          
          'surat-izin-list',
          'surat-izin-create',
          'surat-izin-edit',
          'surat-izin-delete',
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

          'surat-keterangan-list',
          'surat-keterangan-create',
          'surat-keterangan-edit',
          'surat-keterangan-delete',
          'surat-keterangan-approve',

          'template-surat-list',
          'template-surat-create',
          'template-surat-edit',
          'template-surat-delete',

          'user-list',
          'user-create',
          'user-edit',
          'user-delete',

          'grade-list',
          'grade-create',
          'grade-edit',
          'grade-delete',

          'profile-updated-approve'

          ];

          foreach ($permissions as $permission) {
               Permission::updateOrCreate(['name' => $permission]);
          }
          $role          = Role::updateOrCreate(['name' => 'Super Admin']);
          $permissions   = Permission::pluck('id','id')->all();
          
          $role->syncPermissions($permissions);
          
          Role::updateOrCreate(['name'=> 'Admin'])
          ->givePermissionTo(
               [
                    'banner-list',
                    'banner-create',
                    'banner-edit',
                    'banner-delete',

                    'berita-list',
                    'berita-create',
                    'berita-edit',
                    'berita-delete',

                    'kategori-surat-izin-list',

                    'kategori-berita-list',
                    'kategori-berita-create',
                    'kategori-berita-edit',
                    'kategori-berita-delete',

                    'pengaduan-wbs-list',
                    'pengaduan-wbs-create',
                    'pengaduan-wbs-edit',
                    'pengaduan-wbs-delete',

                    'pengaduan-list',
                    'pengaduan-create',
                    'pengaduan-edit',
                    'pengaduan-delete',
                    
                    'profil-edit',

                    'template-surat-list',
                    'template-surat-create',
                    'template-surat-edit',
                    'template-surat-delete',

                    'user-list',
                    'user-create',
                    'user-edit',
                    'user-delete',

                    'grade-list',

                    'profile-updated-approve'
               ]
          );

          Role::updateOrCreate(['name'=> 'Akademik dan Ketarunaan'])
          ->givePermissionTo(
               [
                    'banner-list',

                    'berita-list',

                    'data-keluarga-asuh-list',
                    'data-keluarga-asuh-create',
                    'data-keluarga-asuh-edit',
                    'data-keluarga-asuh-delete',
          
                    'data-pembina-keluarga-asuh-list',
                    'data-pembina-keluarga-asuh-create',
                    'data-pembina-keluarga-asuh-edit',
                    'data-pembina-keluarga-asuh-delete',
          
                    'data-waliasuh-keluarga-asuh-list',
                    'data-waliasuh-keluarga-asuh-create',
                    'data-waliasuh-keluarga-asuh-edit',
                    'data-waliasuh-keluarga-asuh-delete',
          
                    'data-orangtua-taruna-list',
                    'data-orangtua-taruna-create',
                    'data-orangtua-taruna-edit',
                    'data-orangtua-taruna-delete',

                    'kategori-surat-izin-list',
                    
                    'profil-edit',

                    'pengaduan-wbs-list',
                    'pengaduan-wbs-create',
                    'pengaduan-wbs-edit',
                    'pengaduan-wbs-delete',

                    'pengaduan-list',
                    'pengaduan-create',
                    'pengaduan-edit',
                    'pengaduan-delete',

                    'surat-izin-list',
                    'surat-izin-orang-tua-sakit-list',
                    'surat-izin-orang-tua-meninggal-list',
                    'surat-izin-pernikahan-saudara-list',
                    'surat-izin-kegiatan-pesiar-list',
                    'surat-izin-rawat-inap-list',
                    'surat-izin-training-list',
                    'surat-izin-training-approve',
                    'surat-izin-keluar-kampus-list',
                    'surat-izin-keluar-kampus-approve',
                    'surat-izin-kegiatan-dalam-list',
                    'surat-izin-kegiatan-dalam-approve',
                    'surat-tugas-list',
                    'surat-tugas-approve',
                    'prestasi-taruna-list',
                    'hukuman-dinas-list',
                    'hukuman-dinas-approve',
                    'surat-keterangan-list',
                    'surat-keterangan-approve',
                    'pengasuhan-daring-list',
                    'pengasuhan-daring-approve',
                    'absensi-list',
                    'jurnal-harian-list',

                    'template-surat-list',
                    'user-list'
                    
               ]
          );

          Role::updateOrCreate(['name'=> 'Wali Asuh'])
          ->givePermissionTo(
               [
                    'banner-list',

                    'berita-list',

                    'data-keluarga-asuh-list',
          
                    'data-pembina-keluarga-asuh-list',
          
                    'data-waliasuh-keluarga-asuh-list',
          
                    'data-orangtua-taruna-list',

                    'kategori-surat-izin-list',
                    
                    'profil-edit',

                    'pengaduan-wbs-list',
                    'pengaduan-wbs-create',
                    'pengaduan-wbs-edit',
                    'pengaduan-wbs-delete',

                    'pengaduan-list',
                    'pengaduan-create',
                    'pengaduan-edit',
                    'pengaduan-delete',

                    'surat-izin-list',
                    'surat-izin-orang-tua-sakit-list',
                    'surat-izin-orang-tua-meninggal-list',
                    'surat-izin-pernikahan-saudara-list',
                    'surat-izin-kegiatan-pesiar-list',
                    'surat-izin-rawat-inap-list',
                    'surat-izin-training-list',
                    'surat-izin-keluar-kampus-list',
                    'surat-izin-kegiatan-dalam-list',
                    'surat-tugas-list',
                    'prestasi-taruna-list',
                    'hukuman-dinas-list',
                    'surat-keterangan-list',
                    'pengasuhan-daring-list',
                    'pengasuhan-daring-create',
                    'pengasuhan-daring-edit',
                    'pengasuhan-daring-delete',
                    'absensi-list',
                    'jurnal-harian-list',

                    'template-surat-list',
                    'user-list'
               ]
          );

          Role::updateOrCreate(['name'=> 'Pembina'])
          ->givePermissionTo(
               [
                    'data-keluarga-asuh-list',
          
                    'data-pembina-keluarga-asuh-list',
          
                    'data-waliasuh-keluarga-asuh-list',
          
                    'data-orangtua-taruna-list',

                    'kategori-surat-izin-list',
                    
                    'profil-edit',

                    'pengaduan-wbs-list',
                    'pengaduan-wbs-create',
                    'pengaduan-wbs-edit',
                    'pengaduan-wbs-delete',

                    'pengaduan-list',
                    'pengaduan-create',
                    'pengaduan-edit',
                    'pengaduan-delete',

                    'surat-izin-list',

                    'surat-izin-orang-tua-sakit-list',
                    'surat-izin-orang-tua-sakit-approve',
                    'surat-izin-orang-tua-meninggal-list',
                    'surat-izin-orang-tua-meninggal-approve',
                    'surat-izin-pernikahan-saudara-list',
                    'surat-izin-pernikahan-saudara-approve',
                    'surat-izin-kegiatan-pesiar-list',
                    'surat-izin-kegiatan-pesiar-approve',
                    'surat-izin-rawat-inap-list',
                    'surat-izin-rawat-inap-approve',
                    'surat-izin-training-list',
                    'surat-izin-training-approve',
                    'surat-izin-keluar-kampus-list',
                    'surat-izin-keluar-kampus-approve',
                    'surat-izin-kegiatan-dalam-list',
                    'surat-izin-kegiatan-dalam-approve',
                    'surat-tugas-list',
                    'surat-tugas-approve',
                    'prestasi-taruna-list',
                    'prestasi-taruna-approve',
                    'hukuman-dinas-list',
                    'hukuman-dinas-create',
                    'hukuman-dinas-edit',
                    'hukuman-dinas-delete',
                    'surat-keterangan-list',
                    'surat-keterangan-approve',
                    'pengasuhan-daring-list',
                    'pengasuhan-daring-create',
                    'pengasuhan-daring-edit',
                    'pengasuhan-daring-delete',
                    'absensi-list',
                    'jurnal-harian-list',
                    'jurnal-harian-approve',

                    'template-surat-list',
                    'user-list'
               ]
          );

          Role::updateOrCreate(['name'=> 'Direktur'])
          ->givePermissionTo(
               [
                    'banner-list',

                    'berita-list',

                    'data-keluarga-asuh-list',
          
                    'data-pembina-keluarga-asuh-list',
          
                    'data-waliasuh-keluarga-asuh-list',
          
                    'data-orangtua-taruna-list',

                    'kategori-surat-izin-list',
                    
                    'profil-edit',

                    'pengaduan-wbs-list',
                    'pengaduan-wbs-create',
                    'pengaduan-wbs-edit',
                    'pengaduan-wbs-delete',

                    'pengaduan-list',
                    'pengaduan-create',
                    'pengaduan-edit',
                    'pengaduan-delete',

                    'surat-izin-list',
                    'surat-izin-orang-tua-sakit-list',
                    'surat-izin-orang-tua-sakit-approve',
                    'surat-izin-orang-tua-meninggal-list',
                    'surat-izin-orang-tua-meninggal-approve',
                    'surat-izin-pernikahan-saudara-list',
                    'surat-izin-pernikahan-saudara-approve',
                    'surat-izin-kegiatan-pesiar-list',
                    'surat-izin-rawat-inap-list',
                    'surat-izin-rawat-inap-approve',
                    'surat-izin-training-list',
                    'surat-izin-keluar-kampus-list',
                    'surat-izin-kegiatan-dalam-list',
                    'surat-tugas-list',
                    'prestasi-taruna-list',
                    'prestasi-taruna-approve',
                    'hukuman-dinas-list',
                    'surat-keterangan-list',
                    'surat-keterangan-approve',
                    'pengasuhan-daring-list',
                    'absensi-list',
                    'jurnal-harian-list',
                    'jurnal-harian-approve',

                    'template-surat-list',
                    'user-list'
               ]
          );

          Role::updateOrCreate(['name'=> 'Taruna'])
          ->givePermissionTo(
               [
                    'data-orangtua-taruna-list',
                    
                    'profil-edit',

                    'pengaduan-wbs-list',
                    'pengaduan-wbs-create',
                    'pengaduan-wbs-edit',
                    'pengaduan-wbs-delete',

                    'pengaduan-list',
                    'pengaduan-create',
                    'pengaduan-edit',
                    'pengaduan-delete',

                    'surat-izin-list',
                    'surat-izin-create',
                    'surat-izin-edit',
                    'surat-izin-delete',
                    'surat-izin-orang-tua-sakit-list',
                    'surat-izin-orang-tua-meninggal-list',
                    'surat-izin-pernikahan-saudara-list',
                    
                    'surat-izin-kegiatan-pesiar-list',
                    'surat-izin-kegiatan-pesiar-create',
                    'surat-izin-kegiatan-pesiar-edit',
                    'surat-izin-kegiatan-pesiar-delete',
                    
                    'surat-izin-rawat-inap-list',
                    'surat-izin-rawat-inap-create',
                    'surat-izin-rawat-inap-edit',
                    'surat-izin-rawat-inap-delete',
                    
                    'surat-izin-training-list',
                    'surat-izin-training-create',
                    'surat-izin-training-edit',
                    'surat-izin-training-delete',

                    'surat-izin-keluar-kampus-list',
                    'surat-izin-keluar-kampus-create',
                    'surat-izin-keluar-kampus-edit',
                    'surat-izin-keluar-kampus-delete',

                    'surat-izin-kegiatan-dalam-list',
                    'surat-izin-kegiatan-dalam-create',
                    'surat-izin-kegiatan-dalam-edit',
                    'surat-izin-kegiatan-dalam-delete',
                    
                    'surat-tugas-list',
                    'surat-tugas-create',
                    'surat-tugas-edit',
                    'surat-tugas-delete',

                    'prestasi-taruna-list',
                    'prestasi-taruna-create',
                    'prestasi-taruna-edit',
                    'prestasi-taruna-delete',

                    'hukuman-dinas-list',

                    'surat-keterangan-list',
                    'surat-keterangan-create',
                    'surat-keterangan-edit',
                    'surat-keterangan-delete',

                    'pengasuhan-daring-list',

                    'absensi-list',
                    'absensi-create',
                    'absensi-edit',
                    'absensi-delete',

                    'jurnal-harian-list',
                    'jurnal-harian-create',
                    'jurnal-harian-edit',
                    'jurnal-harian-delete'
               ]
          );

          Role::updateOrCreate(['name'=> 'Orang Tua'])
          ->givePermissionTo(
               [
                    'data-orangtua-taruna-list',
                    
                    'profil-edit',

                    'pengaduan-wbs-list',
                    'pengaduan-wbs-create',
                    'pengaduan-wbs-edit',
                    'pengaduan-wbs-delete',

                    'pengaduan-list',
                    'pengaduan-create',
                    'pengaduan-edit',
                    'pengaduan-delete',

                    'surat-izin-list',
                    'surat-izin-orang-tua-sakit-list',
                    'surat-izin-orang-tua-sakit-create',
                    'surat-izin-orang-tua-sakit-edit',
                    'surat-izin-orang-tua-sakit-delete',
                    
                    'surat-izin-orang-tua-meninggal-list',
                    'surat-izin-orang-tua-meninggal-create',
                    'surat-izin-orang-tua-meninggal-edit',
                    'surat-izin-orang-tua-meninggal-delete',

                    'surat-izin-pernikahan-saudara-list',
                    'surat-izin-pernikahan-saudara-create',
                    'surat-izin-pernikahan-saudara-edit',
                    'surat-izin-pernikahan-saudara-delete',
                    
                    'surat-izin-kegiatan-pesiar-list',
                    
                    'surat-izin-rawat-inap-list',
                    
                    'surat-izin-training-list',

                    'surat-izin-keluar-kampus-list',

                    'surat-izin-kegiatan-dalam-list',
                    
                    'surat-tugas-list',

                    'prestasi-taruna-list',

                    'hukuman-dinas-list',

                    'surat-keterangan-list',

                    'pengasuhan-daring-list',

                    'absensi-list',

                    'jurnal-harian-list'
               ]
          );

    }
}
