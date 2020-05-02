<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';
    protected $fillable = [
        'name', 'description', 'product_category_image'
    ];
    protected $primaryKey = 'id';

    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
}
