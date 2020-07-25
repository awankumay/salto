<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaigns';
    protected $fillable = [
        'title',
        'slug',
        'meta_title',
        'meta_description',
        'excerpt',
        'content',
        'headline',
        'status',
        'photo',
        'tags',
        'user_id',
        'user_created',
        'user_updated',
        'date_published',
        'date_started',
        'date_ended',
        'set_fund_target',
        'fund_target',
        'beneficiary_account',
        'beneficiary_account_issuer',
        'beneficiary_account_name'
    ];

    public function Author()
    {
    	return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function GetCount()
    {
        return Campaign::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Campaign::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Campaign::where('title','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Campaign::where('title','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
