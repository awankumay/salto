<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrangTua extends Model
{
    use SoftDeletes;
    protected $table = 'orang_tua_taruna';
    protected $fillable = [ 'orangtua_id', 'taruna_id', 'user_created', 'user_updated', 'user_deleted'];
    protected $dates = ['deleted_at'];
}
