<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products' ;
    protected $fillable = [
        'product_code', 'product_name', 'product_category', 'product_image', 'product_sale', 'cost', 'description'
    ];
}
