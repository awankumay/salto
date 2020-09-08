<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class UserConvicts extends Model
{
    protected $table = 'user_has_convicts';
    protected $fillable = [
        'users_id', 'convicts_id'
    ];
    protected $primaryKey = 'id';
}
