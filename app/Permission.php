<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    
    protected $table = 'menu_persetujuan';
    protected $fillable = [
        'nama_menu', 'user_created', 'user_updated', 'user_deleted'
    ];
    protected $primaryKey = 'id';
    protected $dates = ['deleted_at'];

    public function Author()
    {
    	return $this->belongsTo(User::class, 'author', 'id');
    }

    public function GetCount()
    {
        return Permission::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = Permission::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = Permission::where('nama_menu','LIKE',"%{$search}%")
                                ->orWhere('id', 'LIKE',"%{$search}%")
                                ->offset($start)
                                ->limit($limit)
                                ->orderBy($order,$dir)
                                ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = Permission::where('nama_menu','LIKE',"%{$search}%")
                                ->orWhere('id', 'LIKE',"%{$search}%")
                                ->count();
        return $data;
    }
}
