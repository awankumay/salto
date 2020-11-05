<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PemakamanKeluarga extends Model
{
    use SoftDeletes;
    
    protected $table = 'tb_pemakaman';
    protected $fillable = [
        'stb', 'keperluan', 'tujuan', 'status', 'datetime', 'user_updated', 
        'user_deleted', 'id_surat', 'id_user', 'user_created'
    ];
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

}
