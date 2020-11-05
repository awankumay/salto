<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingCenter extends Model
{
    use SoftDeletes;
    
    protected $table = 'tb_training';
    protected $fillable = [
        'stb', 'nm_tc', 'pelatih', 'status', 'datetime', 'user_updated', 
        'user_deleted', 'id_surat', 'id_user', 'user_created'
    ];
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

}
