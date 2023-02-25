<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'nama'          => $row[0],
            'stb'           => $row[1],
            'email'         => $row[2],
            'identitas'     => $row[3],
            'phone'         => $row[4],
            'whatsapp'      => $row[5],
            'sex'           => $row[6],
            'roles'         => $row[7],
            'status'        => $row[8],
            'orang_tua'     => $row[9],

        ]);
    }
}
