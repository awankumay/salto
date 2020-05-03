<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
        	'name' => 'superadmin',
        	'email' => 'super@nope.id',
        	'password' => bcrypt('123456')
        ]);

        $user->assignRole('Super Admin');
    }
}
