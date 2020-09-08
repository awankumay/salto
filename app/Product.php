<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'name',
        'type',
        'photo',
        'price',
        'id_categories',
        'status'
    ];

    public function GetCount()
    {
        return Product::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Product::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Product::where('name','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Product::where('name','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
