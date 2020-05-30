<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'Posts' ;
    protected $fillable = [
        'product_code', 'product_name', 'product_category', 'product_image', 'product_sale', 'product_cost', 'product_description'
    ];
    protected $primaryKey = 'id';

    public function ProductCategory()
    {
        return $this->hasMany(ProductCategory::class, 'id', 'product_category');
    }
}
