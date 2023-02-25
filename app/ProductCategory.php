<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class ProductCategory extends Model
{
    protected $table = 'product_categories';
    protected $fillable = [
        'name', 'description', 'author', 'user_created', 'user_updated'
    ];
    protected $primaryKey = 'id';

    public function Author()
    {
    	return $this->belongsTo(User::class, 'author', 'id');
    }

    public function Auction()
    {
    	return $this->belongsTo(Auction::class, 'product_categories_id', 'id');
    }

    public function GetCount()
    {
        return ProductCategory::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = ProductCategory::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = ProductCategory::where('description','LIKE',"%{$search}%")
                                ->orWhere('name', 'LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = ProductCategory::where('description','LIKE',"%{$search}%")
                                ->orWhere('name', 'LIKE',"%{$search}%")
                                ->count();
        return $data;
    }
}
