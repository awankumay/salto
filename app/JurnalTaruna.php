<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use DB;

class JurnalTaruna extends Model
{
    protected $fillable = ['id_user', 'tanggal', 'start_time', 'end_time', 'kegiatan', 'status', 'grade'];
    protected $table = 'jurnal_taruna';
    protected $casts = [
        'created_at'  => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s'
    ];
    public function GetCount()
    {
        return JurnalTaruna::groupBy('tanggal')->count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = JurnalTaruna::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->groupBy('tanggal')
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = JurnalTaruna::where('name','LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->groupBy('tanggal')
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = JurnalTaruna::where('name','LIKE',"%{$search}%")
                            ->groupBy('tanggal')
                            ->count();
        return $data;
    }
}
