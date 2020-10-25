<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
#use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use DB;

class User extends Authenticatable
{
    #use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'identity', 'stb', 'email', 'password', 'phone', 
        'whatsapp', 'address', 'description', 'tagline', 'sex', 'status', 'photo', 
        'user_created', 'user_updated', 'user_deleted', 'grade', 'province_id', 'regencie_id'
    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function GetCount()
    {
        return User::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = User::offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = User::where('name','LIKE',"%{$search}%")
                            ->orWhere('phone', 'LIKE',"%{$search}%")
                            ->orWhere('whatsapp', 'LIKE',"%{$search}%")
                            ->orWhere('email', 'LIKE',"%{$search}%")
                            ->orWhere('stb', 'LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = User::where('name','LIKE',"%{$search}%")
                            ->orWhere('phone', 'LIKE',"%{$search}%")
                            ->orWhere('whatsapp', 'LIKE',"%{$search}%")
                            ->orWhere('email', 'LIKE',"%{$search}%")
                            ->orWhere('stb', 'LIKE',"%{$search}%")
                            ->count();
        return $data;
    }

    public static function GetOrangTua($id=null){
        if($id!==null){
            $user = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('orang_tua_taruna', 'users.id', '=', 'orang_tua_taruna.orangtua_id')
            ->select('users.id', 'users.name')
            ->where('model_has_roles.role_id', 8)
            ->whereNull('users.deleted_at')
            ->whereNotIn('users.id', function ($query) use ($id) {
                $query->select('orangtua_id')->from('orang_tua_taruna')->whereNull('deleted_at')
                ->where('taruna_id', '!=', $id);
            })
            ->get();
        }else{
            $user = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->select('users.id', 'users.name')
            ->where('model_has_roles.role_id', 8)
            ->whereNull('users.deleted_at')
            ->whereNotIn('users.id', function ($query) {
                $query->select('orangtua_id')->from('orang_tua_taruna')->whereNull('deleted_at');
            })->get();
        }

        return $user;
    }
}
