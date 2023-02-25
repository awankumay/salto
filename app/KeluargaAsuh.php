<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeluargaAsuh extends Model
{
    use SoftDeletes;
    
    protected $table = 'keluarga_asuh';
    protected $fillable = [
        'name', 'description', 'user_created', 'user_updated', 'user_deleted'
    ];
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    public function GetCount()
    {
        return KeluargaAsuh::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = KeluargaAsuh::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = KeluargaAsuh::where('description','LIKE',"%{$search}%")
                                ->orWhere('name', 'LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = KeluargaAsuh::where('description','LIKE',"%{$search}%")
                                ->orWhere('name', 'LIKE',"%{$search}%")
                                ->count();
        return $data;
    }
}
