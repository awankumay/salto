<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Materiwbs extends Model
{
    use SoftDeletes;
    
    protected $table = 'materi_wbs';
    protected $fillable = [ 'id', 'nama_materi'];
    // protected $primaryKey = 'id';
    // protected $dates = ['deleted_at'];

    // public function GetCount()
    // {
    //     return Materiwbs::count();
    // }

    // public function GetCurrentData($start, $limit, $order, $dir)
    // {
    //     $data = Materiwbs::offset($start)
    //                         ->limit($limit)
    //                         ->orderBy($order,$dir)
    //                         ->get();
    //     return $data;
    // }

    // public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    // {
    //     $data = Materiwbs::where('nama_materi','LIKE',"%{$search}%")
    //                             ->orWhere('id', 'LIKE',"%{$search}%")
    //                             ->offset($start)
    //                             ->limit($limit)
    //                             ->orderBy($order,$dir)
    //                             ->get();
    //     return $data;
    // }

    // public function GetCountDataFilter($search){
    //     $data = Materiwbs::where('nama_materi','LIKE',"%{$search}%")
    //                             ->orWhere('id', 'LIKE',"%{$search}%")
    //                             ->count();
    //     return $data;
    // }
}
