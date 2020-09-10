<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Visit extends Model
{
    protected $table = 'appointments';
    protected $fillable = [
        'id_users', 'visitor_name', 'type', 'schedule', 'date', 'status', 'no_antrian', 'convicts_id'
    ];
    protected $primaryKey = 'id';
    public function GetCount()
    {
        return Visit::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Visit::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Visit::where('name','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Visit::where('name','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
