<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class WaliasuhKeluargaAsuh extends Model
{
    //use SoftDeletes;
    
    protected $table = 'waliasuh_keluarga_asuh';
    protected $fillable = [
        'keluarga_asuh_id', 'waliasuh_id', 'user_created', 'user_updated', 'user_deleted'
    ];
    protected $primaryKey = 'id';
    //protected $dates = ['deleted_at'];

    public function GetCount($id)
    {
        return WaliasuhKeluargaAsuh::where('keluarga_asuh_id', $id)->count();
    }

    public function GetCurrentData($start, $limit, $order, $dir, $id)
    {
        $data = WaliasuhKeluargaAsuh::join('keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'keluarga_asuh.id')
                    ->join('users', 'users.id', '=', 'waliasuh_keluarga_asuh.waliasuh_id')
                    ->select('waliasuh_keluarga_asuh.id as id', 'users.name', 'users.phone', 'users.whatsapp', 'waliasuh_keluarga_asuh.created_at as date_created')
                    ->where('waliasuh_keluarga_asuh.keluarga_asuh_id', $id)
                    ->whereNull('keluarga_asuh.deleted_at')
                    ->whereNull('users.deleted_at')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search, $id)
    {
        $params['id'] = $id;
        $params['search'] = $search;
        $data = WaliasuhKeluargaAsuh::join('keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'keluarga_asuh.id')
                    ->join('users', 'users.id', '=', 'waliasuh_keluarga_asuh.waliasuh_id')
                    ->select('waliasuh_keluarga_asuh.id', 'users.name', 'users.phone', 'users.whatsapp', 'waliasuh_keluarga_asuh.created_at as date_created')
                    ->where(function($q) use ($params) {
                        $q->whereNull('keluarga_asuh.deleted_at')
                        ->where('users.name', 'LIKE', "%{$params['search']}%")
                        ->where('waliasuh_keluarga_asuh.keluarga_asuh_id', $params['id']);
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

        return $data;
    }

    public function GetCountDataFilter($search, $id){
        $params['id'] = $id;
        $params['search'] = $search;
        $data = WaliasuhKeluargaAsuh::join('keluarga_asuh', 'waliasuh_keluarga_asuh.keluarga_asuh_id', '=', 'keluarga_asuh.id')
                    ->join('users', 'users.id', '=', 'waliasuh_keluarga_asuh.waliasuh_id')
                    ->select('waliasuh_keluarga_asuh.id', 'users.name', 'users.phone', 'users.whatsapp')
                    ->Where(function($q) use ($params) {
                        $q->whereNull('keluarga_asuh.deleted_at')
                        ->where('users.name', 'LIKE', "%{$params['search']}%")
                        ->where('waliasuh_keluarga_asuh.keluarga_asuh_id', $params['id']);
                    })
                    ->count();

        return $data;
    }
}
