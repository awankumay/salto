<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'stores';
    protected $fillable = [
        'store_code', 'store_name', 'phone_number', 'address'
    ];
}
