<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';
    protected $fillable = [
        'name', 'description'
    ];
    protected $primaryKey = 'id';

    public function Product()
    {
        return $this->belongsTo(Product::class);
    }
}
