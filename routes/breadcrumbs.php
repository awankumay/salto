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
Breadcrumbs::register('tags', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Tags', route('tags.index'));
});
Breadcrumbs::register('tags.create', function ($breadcrumbs) {
    $breadcrumbs->parent('tags');
    $breadcrumbs->push('Tambah Tags', route('tags.create'));
});
Breadcrumbs::register('tags.edit', function ($breadcrumbs, $tags) {
    $breadcrumbs->parent('tags');
    $breadcrumbs->push($tags->name, route('tags.edit', $tags->id));
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
Breadcrumbs::register('auction', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Auction', route('auction.index'));
});
Breadcrumbs::register('auction.create', function ($breadcrumbs) {
    $breadcrumbs->parent('auction');
    $breadcrumbs->push('Tambah Auction', route('auction.create'));
});
Breadcrumbs::register('auction.edit', function ($breadcrumbs, $auction) {
    $breadcrumbs->parent('auction');
    $breadcrumbs->push($auction->title, route('auction.edit', $auction->id));
});
Breadcrumbs::register('product-category', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Produk kategori', route('product-category.index'));
});
Breadcrumbs::register('product-category.create', function ($breadcrumbs) {
    $breadcrumbs->parent('product-category');
    $breadcrumbs->push('Tambah produk kategori', route('product-category.create'));
});
Breadcrumbs::register('product-category.edit', function ($breadcrumbs, $productCategory) {
    $breadcrumbs->parent('product-category');
    $breadcrumbs->push($productCategory->name, route('product-category.edit', $productCategory->id));
});
Breadcrumbs::register('convict', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Tahanan', route('convict.index'));
});
Breadcrumbs::register('convict.create', function ($breadcrumbs) {
    $breadcrumbs->parent('convict');
    $breadcrumbs->push('Tambah Tahanan', route('convict.create'));
});
Breadcrumbs::register('convict.edit', function ($breadcrumbs, $convict) {
    $breadcrumbs->parent('convict');
    $breadcrumbs->push($convict->name, route('convict.edit', $convict->id));
});
Breadcrumbs::register('product', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Produk', route('product.index'));
});
Breadcrumbs::register('product.create', function ($breadcrumbs) {
    $breadcrumbs->parent('product');
    $breadcrumbs->push('Tambah produk', route('product.create'));
});
Breadcrumbs::register('product.edit', function ($breadcrumbs, $product) {
    $breadcrumbs->parent('product');
    $breadcrumbs->push($product->name, route('product.edit', $product->id));
});
Breadcrumbs::register('visitor', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Visitor', route('visitor.index'));
});
Breadcrumbs::register('visitor.create', function ($breadcrumbs) {
    $breadcrumbs->parent('visitor');
    $breadcrumbs->push('Tambah produk', route('visitor.create'));
});
Breadcrumbs::register('visitor.edit', function ($breadcrumbs, $visitor) {
    $breadcrumbs->parent('visitor');
    $breadcrumbs->push($visitor->name, route('visitor.edit', $visitor->id));
});
Breadcrumbs::register('transaction', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Daftar belanja', route('transaction.index'));
});
Breadcrumbs::register('transaction.create', function ($breadcrumbs) {
    $breadcrumbs->parent('transaction');
    $breadcrumbs->push('Tambah produk', route('transaction.create'));
});
Breadcrumbs::register('transaction.edit', function ($breadcrumbs, $transaction) {
    $breadcrumbs->parent('transaction');
    $breadcrumbs->push($transaction->id, route('transaction.edit', $transaction->id));
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
Breadcrumbs::register('rating', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('rating', route('rating.index'));
});
Breadcrumbs::register('rating.create', function ($breadcrumbs) {
    $breadcrumbs->parent('rating');
    $breadcrumbs->push('Tambah rating', route('rating.create'));
});
Breadcrumbs::register('rating.edit', function ($breadcrumbs, $rating) {
    $breadcrumbs->parent('rating');
    $breadcrumbs->push($rating->id, route('rating.edit', $rating->id));
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


?>
