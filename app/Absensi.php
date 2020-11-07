<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    //use SoftDeletes;
    protected $fillable = ['clock_in', 'clock_out', 'file_clock_in', 'file_clock_out', 'id_user', 'created_at', 'updated_at'];
   // protected $dates = ['deleted_at'];
    protected $table = 'absensi_taruna';

    public function GetCount()
    {
        return Absensi::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Absensi::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Absensi::where('name','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Absensi::where('name','LIKE',"%{$search}%")
                            ->count();
        return $data;
    }
}
