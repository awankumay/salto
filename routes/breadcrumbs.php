<?php

Breadcrumbs::register('home', function ($breadcrumbs) {
     $breadcrumbs->push('HOME', route('home'));
});
Breadcrumbs::register('user', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('USER', route('user.index'));
});
Breadcrumbs::register('user.create', function ($breadcrumbs) {
    $breadcrumbs->parent('user');
    $breadcrumbs->push('ADD NEW USER', route('user.create'));
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
Breadcrumbs::register('store', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('store', route('store.index'));
});
Breadcrumbs::register('store.create', function ($breadcrumbs) {
    $breadcrumbs->parent('store');
    $breadcrumbs->push('add new store', route('store.create'));
});
Breadcrumbs::register('store.edit', function ($breadcrumbs, $store) {
    $breadcrumbs->parent('store');
    $breadcrumbs->push($store->store_name.' ('.$store->store_code.')', route('store.edit', $store->id));
});
Breadcrumbs::register('product-category', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('product category', route('product-category.index'));
});
Breadcrumbs::register('product-category.create', function ($breadcrumbs) {
    $breadcrumbs->parent('product-category');
    $breadcrumbs->push('add new product category', route('product-category.create'));
});
Breadcrumbs::register('product-category.edit', function ($breadcrumbs, $productCategory) {
    $breadcrumbs->parent('product-category');
    $breadcrumbs->push($productCategory->name, route('product-category.edit', $productCategory->id));
});
Breadcrumbs::register('product', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('product', route('product.index'));
});
Breadcrumbs::register('product.create', function ($breadcrumbs) {
    $breadcrumbs->parent('product');
    $breadcrumbs->push('add new product', route('product.create'));
});
Breadcrumbs::register('product.edit', function ($breadcrumbs, $product) {
    $breadcrumbs->parent('product');
    $breadcrumbs->push($product->name, route('product.edit', $product->id));
});



?>
