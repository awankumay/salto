<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $table = 'auctions';
    protected $fillable = [
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'excerpt',
        'product_name',
        'product_categories_id',
        'product_categories_name',
        'tags',
        'content',
        'photo',
        'headline',
        'status',
        'user_id',
        'user_created',
        'user_updated',
        'date_published',
        'date_started',
        'date_ended',
        'buy_now',
        'price_buy_now',
        'start_price',
        'multiple_bid',
        'rate_donation',
        'beneficiary_account',
        'beneficiary_account_issuer',
        'beneficiary_account_name'
    ];

    public function Author()
    {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ProductCategory(){
    	return $this->hasOne(ProductCategory::class, 'id', 'product_categories_id');
    }

    public function GetCount()
    {
        return Auction::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Auction::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Auction::where('title','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Auction::where('title','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
