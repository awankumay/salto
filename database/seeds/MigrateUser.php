<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;

class MigrateUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::doesntHave('roles')->get();
        foreach ($user as $key => $value) {
            User::find($value->id)->assignRole('Orang Tua');
        }
        
    }
}

/* INSERT INTO users (stb, email, name, password)
SELECT a.username as stb, concat(a.username, '@mailinator.com') as email, 
b.nama as name, '$2y$10$XcVLJTyyJf9CtdDeCp149.tdvtdhGyVxjk9EgrGH/YFUokGctzv0u' as password
from login a
left join tb_profil b on a.username=b.username
where akses = 3  */
