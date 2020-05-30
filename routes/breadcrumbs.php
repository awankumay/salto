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
    $breadcrumbs->push('Kategori Konten', route('post-category.index'));
});
Breadcrumbs::register('post-category.create', function ($breadcrumbs) {
    $breadcrumbs->parent('post-category');
    $breadcrumbs->push('Tambah Kategori Konten', route('post-category.create'));
});
Breadcrumbs::register('post-category.edit', function ($breadcrumbs, $postCategory) {
    $breadcrumbs->parent('post-category');
    $breadcrumbs->push($postCategory->name, route('post-category.edit', $postCategory->id));
});
Breadcrumbs::register('post', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Konten', route('post.index'));
});
Breadcrumbs::register('post.create', function ($breadcrumbs) {
    $breadcrumbs->parent('post');
    $breadcrumbs->push('Tambah Konten', route('post.create'));
});
Breadcrumbs::register('post.edit', function ($breadcrumbs, $post) {
    $breadcrumbs->parent('post');
    $breadcrumbs->push($post->name, route('post.edit', $post->id));
});



?>
