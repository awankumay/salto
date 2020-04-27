<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
            'product-category-list',
            'product-category-create',
            'product-category-edit',
            'product-category-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'store-list',
            'store-create',
            'store-edit',
            'store-delete'
         ];


         foreach ($permissions as $permission) {
              Permission::updateOrCreate(['name' => $permission]);
         }
    }
}
