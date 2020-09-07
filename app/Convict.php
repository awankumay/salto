<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Convict extends Model
{
    protected $table = 'convicts';
    protected $fillable = [
        'unique_id',
        'identity',
        'identity_tipe',
        'name',
        'type_convict',
        'photo',
        'document',
        'violation',
        'clause',
        'date_start',
        'date_end',
        'address',
        'block',
        'lockup',
        'user_created'
    ];

    public function GetCount()
    {
        return Convict::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Convict::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Convict::where('name','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Convict::where('name','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
