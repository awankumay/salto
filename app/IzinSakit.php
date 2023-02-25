<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IzinSakit extends Model
{
    use SoftDeletes;
    
    protected $table = 'tb_izin_sakit';
    protected $fillable = [
        'stb', 'keluhan', 'diagnosa', 'rekomendasi', 'dokter', 'status', 'datetime', 'user_updated', 
        'user_deleted', 'id_surat', 'id_user', 'user_created'
    ];
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];
    protected $casts = [
        'created_at'  => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}
