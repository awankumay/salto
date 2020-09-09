<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class SurveyIkm extends Model
{
    protected $table = 'ratings';
    protected $fillable = [
        'users_id', 'rating'
    ];
    protected $primaryKey = 'id';
}
