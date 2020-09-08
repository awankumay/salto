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
    $breadcrumbs->push('Konten', route('content.index'));
});
Breadcrumbs::register('content.create', function ($breadcrumbs) {
    $breadcrumbs->parent('content');
    $breadcrumbs->push('Tambah Konten', route('content.create'));
});
Breadcrumbs::register('content.edit', function ($breadcrumbs, $content) {
    $breadcrumbs->parent('content');
    $breadcrumbs->push($content->title, route('content.edit', $content->id));
});
Breadcrumbs::register('campaign', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Campaign', route('campaign.index'));
});
Breadcrumbs::register('campaign.create', function ($breadcrumbs) {
    $breadcrumbs->parent('campaign');
    $breadcrumbs->push('Tambah Campaign', route('campaign.create'));
});
Breadcrumbs::register('campaign.edit', function ($breadcrumbs, $campaign) {
    $breadcrumbs->parent('campaign');
    $breadcrumbs->push($campaign->title, route('campaign.edit', $campaign->id));
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


?>
