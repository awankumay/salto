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
            'convict-list',
            'convict-create',
            'convict-edit',
            'convict-delete',
            'product-category-list',
            'product-category-create',
            'product-category-edit',
            'product-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'post-category-list',
            'post-category-create',
            'post-category-edit',
            'post-category-delete',
            'post-list',
            'post-create',
            'post-edit',
            'post-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'transaction-list',
            'transaction-create',
            'transaction-edit',
            'transaction-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'visitor-list',
            'visitor-create',
            'visitor-edit',
            'visitor-delete'
         ];

         foreach ($permissions as $permission) {
              Permission::updateOrCreate(['name' => $permission]);
         }

         $role = Role::updateOrCreate(['name' => 'Super Admin']);
         $permissions = Permission::pluck('id','id')->all();
         $role->syncPermissions($permissions);
         Role::updateOrCreate(['name'=> 'Admin'])->givePermissionTo(['product-category-list', 'post-category-list']);
         Role::updateOrCreate(['name'=> 'User'])->givePermissionTo(['product-category-list', 'post-category-list']);
    }
}
