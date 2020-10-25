<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use SoftDeletes;
    protected $fillable = ['grade', 'user_created', 'user_updated', 'user_deleted'];
    protected $dates = ['deleted_at'];
    protected $table = 'grade_table';

    public function GetCount()
    {
        return Grade::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Grade::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Grade::where('name','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Grade::where('name','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
