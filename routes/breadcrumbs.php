<?php

Breadcrumbs::register('home', function ($breadcrumbs) {
     $breadcrumbs->push('Beranda', route('home'));
});
Breadcrumbs::register('user', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Pengguna', route('user.index'));
});
Breadcrumbs::register('user.create', function ($breadcrumbs) {
    $breadcrumbs->parent('user');
    $breadcrumbs->push('Tambah Pengguna', route('user.create'));
});
Breadcrumbs::register('user.edit', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('user');
    $breadcrumbs->push($user->name, route('user.edit', $user->id));
});
Breadcrumbs::register('role', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('role', route('role.index'));
});
Breadcrumbs::register('role.create', function ($breadcrumbs) {
    $breadcrumbs->parent('role');
    $breadcrumbs->push('add new role', route('role.create'));
});
Breadcrumbs::register('role.edit', function ($breadcrumbs, $role) {
    $breadcrumbs->parent('role');
    $breadcrumbs->push($role->name, route('role.edit', $role->id));
});
Breadcrumbs::register('post-category', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Kategori Berita', route('post-category.index'));
});
Breadcrumbs::register('post-category.create', function ($breadcrumbs) {
    $breadcrumbs->parent('post-category');
    $breadcrumbs->push('Tambah Kategori Berita', route('post-category.create'));
});
Breadcrumbs::register('post-category.edit', function ($breadcrumbs, $postCategory) {
    $breadcrumbs->parent('post-category');
    $breadcrumbs->push($postCategory->name, route('post-category.edit', $postCategory->id));
});
Breadcrumbs::register('content', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Berita & Informasi', route('content.index'));
});
Breadcrumbs::register('content.create', function ($breadcrumbs) {
    $breadcrumbs->parent('content');
    $breadcrumbs->push('Tambah Berita & Informasi', route('content.create'));
});
Breadcrumbs::register('content.edit', function ($breadcrumbs, $content) {
    $breadcrumbs->parent('content');
    $breadcrumbs->push($content->title, route('content.edit', $content->id));
});
Breadcrumbs::register('permission', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Permission', route('permission.index'));
});
Breadcrumbs::register('permission.create', function ($breadcrumbs) {
    $breadcrumbs->parent('permission');
    $breadcrumbs->push('Tambah Kategori Surat', route('permission.create'));
});
Breadcrumbs::register('permission.edit', function ($breadcrumbs, $permission) {
    $breadcrumbs->parent('permission');
    $breadcrumbs->push($permission->nama_menu, route('permission.edit', $permission->id));
});
Breadcrumbs::register('slider', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Banner', route('slider.index'));
});
Breadcrumbs::register('slider.create', function ($breadcrumbs) {
    $breadcrumbs->parent('slider');
    $breadcrumbs->push('Tambah Banner', route('slider.create'));
});
Breadcrumbs::register('slider.edit', function ($breadcrumbs, $slider) {
    $breadcrumbs->parent('slider');
    $breadcrumbs->push($slider->id, route('slider.edit', $slider->id));
});
Breadcrumbs::register('pengaduan', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('pengaduan', route('report.index'));
});
Breadcrumbs::register('report.create', function ($breadcrumbs) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push('Tambah report', route('report.create'));
});
Breadcrumbs::register('report.edit', function ($breadcrumbs, $report) {
    $breadcrumbs->parent('report');
    $breadcrumbs->push($report->id, route('report.edit', $report->id));
});
Breadcrumbs::register('grade', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Grade', route('grade.index'));
});
Breadcrumbs::register('grade.create', function ($breadcrumbs) {
    $breadcrumbs->parent('grade');
    $breadcrumbs->push('Tambah Grade', route('grade.create'));
});
Breadcrumbs::register('grade.edit', function ($breadcrumbs, $grade) {
    $breadcrumbs->parent('grade');
    $breadcrumbs->push($grade->grade, route('grade.edit', $grade->id));
});
Breadcrumbs::register('keluarga-asuh', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Keluarga Asuh', route('keluarga-asuh.index'));
});
Breadcrumbs::register('keluarga-asuh.create', function ($breadcrumbs) {
    $breadcrumbs->parent('keluarga-asuh');
    $breadcrumbs->push('Tambah Keluarga Asuh', route('keluarga-asuh.create'));
});
Breadcrumbs::register('keluarga-asuh.edit', function ($breadcrumbs, $keluargaAsuh) {
    $breadcrumbs->parent('keluarga-asuh');
    $breadcrumbs->push($keluargaAsuh->name, route('keluarga-asuh.edit', $keluargaAsuh->id));
});
Breadcrumbs::register('keluarga-asuh.show', function ($breadcrumbs, $keluargaAsuh) {
    $breadcrumbs->parent('keluarga-asuh');
    $breadcrumbs->push($keluargaAsuh->name, route('keluarga-asuh.show', $keluargaAsuh->id));
});
Breadcrumbs::register('surat-izin', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Surat Izin', route('surat-izin.index'));
});
Breadcrumbs::register('surat-izin.create', function ($breadcrumbs) {
    $breadcrumbs->parent('surat-izin');
    $breadcrumbs->push('Tambah Surat Izin', route('surat-izin.create'));
});
Breadcrumbs::register('surat-izin.edit', function ($breadcrumbs, $suratIzin) {
    $breadcrumbs->parent('surat-izin');
    $breadcrumbs->push($suratIzin->name, route('surat-izin.edit', $suratIzin->id));
});
Breadcrumbs::register('surat-izin.show', function ($breadcrumbs, $suratIzin) {
    $breadcrumbs->parent('surat-izin');
    $breadcrumbs->push($suratIzin->name, route('surat-izin.show', $suratIzin->id));
});


?>
