<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Materiwbs extends Model
{
    protected $table = 'materi_wbs';
    protected $fillable = [ 'id', 'nama_materi'];
}
