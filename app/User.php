<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;
use DB;
use Auth;

class User extends Authenticatable
{
    //use Notifiable;
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
        'user_created', 'user_updated', 'user_deleted', 'grade', 'province_id', 'regencie_id',
        'fcm_id'
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
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]=='Super Admin') {
            return User::count();
        }else{
            return User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.role_id', '!=', 1)->count();
        }
        
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]=='Super Admin') {
            $data = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->select('users.id', 'users.name', 'roles.name as role', 'users.photo', 'users.status', 'users.sex', 'users.stb', 'users.phone', 'users.whatsapp', 'users.email')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }else{
            $data = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->where('model_has_roles.role_id', '!=', 1)
                        ->select('users.id', 'users.name', 'roles.name as role', 'users.photo', 'users.status', 'users.sex', 'users.stb', 'users.phone', 'users.whatsapp', 'users.email')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }
       
       
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]=='Super Admin') {
            $data = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->where('users.name','LIKE',"%{$search}%")
                        ->orWhere('users.phone', 'LIKE',"%{$search}%")
                        ->orWhere('users.whatsapp', 'LIKE',"%{$search}%")
                        ->orWhere('users.email', 'LIKE',"%{$search}%")
                        ->orWhere('users.stb', 'LIKE',"%{$search}%")
                        ->select('users.id', 'users.name', 'roles.name as role', 'users.photo', 'users.status', 'users.sex', 'users.stb', 'users.phone', 'users.whatsapp', 'users.email')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }else{
            $data = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                        ->where('model_has_roles.role_id', '!=', 1)
                        ->Where(function($q) use ($search) {
                            $q->where('users.name','LIKE',"%{$search}%")
                            ->orWhere('users.phone', 'LIKE',"%{$search}%")
                            ->orWhere('users.whatsapp', 'LIKE',"%{$search}%")
                            ->orWhere('users.email', 'LIKE',"%{$search}%")
                            ->orWhere('users.stb', 'LIKE',"%{$search}%");
                        })
                        ->select('users.id', 'users.name', 'roles.name as role', 'users.photo', 'users.status', 'users.sex', 'users.stb', 'users.phone', 'users.whatsapp', 'users.email')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }
        return $data;
    }

    public function GetCountDataFilter($search){
        $currentUser = Auth::user();
        if ($currentUser->getRoleNames()[0]=='Super Admin') {
            $data = User::where('name','LIKE',"%{$search}%")
                            ->orWhere('users.phone', 'LIKE',"%{$search}%")
                            ->orWhere('users.whatsapp', 'LIKE',"%{$search}%")
                            ->orWhere('users.email', 'LIKE',"%{$search}%")
                            ->orWhere('users.stb', 'LIKE',"%{$search}%")
                            ->count();
        }else{
            $data = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.role_id', '!=', 1)
                        ->Where(function($q) use ($search) {
                            $q->where('users.name','LIKE',"%{$search}%")
                            ->orWhere('users.phone', 'LIKE',"%{$search}%")
                            ->orWhere('users.whatsapp', 'LIKE',"%{$search}%")
                            ->orWhere('users.email', 'LIKE',"%{$search}%")
                            ->orWhere('users.stb', 'LIKE',"%{$search}%");
                        })
                        ->count();
        }
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

    public static function setInfo($roles, $user)
    {
        $data = [];
        $data['keluarga_asuh'] = [];
        $data['subscribe'] = ['salto'];

        if($roles['0']=='Taruna'){
            $keluarga = TarunaKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                                ->where('taruna_keluarga_asuh.taruna_id', $user->id)
                                ->first();
            if(!empty($keluarga)){
                $keluarga_asuh = strtolower($keluarga->name);
                $data['keluarga_asuh'] = $keluarga_asuh;
                $data['subscribe']  = ['salto', 
                                        'taruna', 
                                        Str::slug($keluarga_asuh, '-'), 
                                        'taruna-'.Str::slug($keluarga_asuh, '-'), 
                                        'grade-'.$user->grade,
                                        'taruna-'.$user->id];
            }
        }else if ($roles['0']=='Orang Tua') {
            $taruna = OrangTua::where('orangtua_id', $user->id)->first();
            $data['subscribe'] = ['salto'];
            if(!empty($taruna)){
                $data['subscribe']   = ['salto', 'orangtua', 'orangtua-'.$taruna->taruna_id, Str::slug('taruna-'.$taruna->taruna_id, '-')];
            }
        }else if ($roles['0']=='Pembina') {
            $keluarga = PembinaKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'pembina_keluarga_asuh.keluarga_asuh_id')
                                ->where('pembina_keluarga_asuh.pembina_id', $user->id)
                                ->first();
            if(!empty($keluarga)){
                $keluarga_asuh = strtolower($keluarga->name);
                $data['keluarga_asuh'] = $keluarga_asuh;
                $data['subscribe']   = [ 'salto',
                                         Str::slug($keluarga_asuh, '-'),
                                         'pembina', 
                                         Str::slug('pembina-'.$keluarga_asuh, '-')];
            }
        }else if ($roles['0']=='Wali Asuh') {
            $keluarga = WaliasuhKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'waliasuh_keluarga_asuh.keluarga_asuh_id')
                            ->where('waliasuh_keluarga_asuh.waliasuh_id', $user->id)
                            ->first();
                            if(!empty($keluarga)){
                                $keluarga_asuh = strtolower($keluarga->name);
                                $data['keluarga_asuh'] = $keluarga_asuh;
                                $data['subscribe']   = ['salto',
                                                         Str::slug($keluarga_asuh, '-'),
                                                         'waliasuh', 
                                                         Str::slug('waliasuh-'.$keluarga_asuh, '-')];
                            }
          
        }else if ($roles['0']=='Akademik dan Ketarunaan') {
            $data['subscribe']   = ['salto', 'akademik-dan-ketarunaan'];
            $data['keluarga_asuh'] = null;
        }else if ($roles['0']=='Direktur') {
            $data['subscribe']   = ['salto', 'direktur'];
            $data['keluarga_asuh'] = null;
        }else if ($roles['0']=='Admin') {
            $data['subscribe']   = ['salto', 'admin'];
            $data['keluarga_asuh'] = null;
        }else if ($roles['0']=='Super Admin') {
            $data['subscribe']   = ['salto', 'super-admin'];
            $data['keluarga_asuh'] = null;
        }else{
            $data['keluarga_asuh'] = null;
            $data['subscribe'] = null;
        }
        return $data;
    }

    public static function topicbackup($category, $data)
    {
        
        switch ($category) {
            case 'createsurat':
                $data = ['pembina', 'waliasuh-'.strtolower(Str::slug($data['keluarga_asuh'], '-'))];
                return $data;
                break;
            case 'disposisisurat':
                $data = ['taruna-'.$data['id'], 'akademik-dan-ketarunaan'];
                return $data;
                break;
            case 'approve-aak':
                $data = ['taruna-'.$data['id'], 
                        'pembina', 
                        'waliasuh-'.strtolower(Str::slug($data['keluarga_asuh'], '-'))];
                return $data;
                break;
            case 'approve-direktur':
                $data = ['taruna-'.$data['id'], 
                        'pembina', 
                        'waliasuh-'.strtolower(Str::slug($data['keluarga_asuh'], '-'))];
                return $data;
                break;
            case 'createhukdis':
                $data = ['taruna-'.$data['id'],
                        'waliasuh-'.strtolower(Str::slug($data['keluarga_asuh'], '-')),
                        'akademik-dan-ketarunaan'];  
                return $data;
            case 'createpengasuhan':
                $data = ['taruna-'.strtolower(Str::slug($data['keluarga_asuh'], '-')),
                        'pembina-'.strtolower(Str::slug($data['keluarga_asuh'], '-'))]; 
                return $data; 
            case 'wbs':
                $data = ['admin']; 
            case 'pengaduan':
                $data = ['admin']; 
                return $data;         
            default:
                $data = [];
                return $data;
                break;
        }
    }

    public static function topic($category, $data)
    {
        
        switch ($category) {
            case 'createsurat':
                $data = ['pembina', 'waliasuh-'.strtolower(Str::slug($data['keluarga_asuh'], '-'))];
                return $data;
                break;
            case 'disposisisurat':
                $data = ['taruna-'.$data['id'], 'akademik-dan-ketarunaan'];
                return $data;
                break;
            case 'approve-aak':
                $data = ['taruna-'.$data['id'], 
                        'pembina', 
                        'waliasuh-'.strtolower(Str::slug($data['keluarga_asuh'], '-'))];
                return $data;
                break;
            case 'approve-direktur':
                $data = ['taruna-'.$data['id'], 
                        'pembina', 
                        'waliasuh-'.strtolower(Str::slug($data['keluarga_asuh'], '-'))];
                return $data;
                break;
            case 'createhukdis':
                $data = ['taruna-'.$data['id'],
                        'waliasuh-'.strtolower(Str::slug($data['keluarga_asuh'], '-')),
                        'akademik-dan-ketarunaan'];  
                return $data;
            case 'createpengasuhan':
                $data = ['taruna-'.strtolower(Str::slug($data['keluarga_asuh'], '-')),
                        'pembina']; 
                return $data; 
            case 'wbs':
                $data = ['admin']; 
            case 'pengaduan':
                $data = ['admin']; 
                return $data;         
            default:
                $data = [];
                return $data;
                break;
        }
    }

    public static function keluargataruna($taruna_id)
    {
        return TarunaKeluargaAsuh::join('keluarga_asuh', 'keluarga_asuh.id', '=', 'taruna_keluarga_asuh.keluarga_asuh_id')
                        ->where('taruna_keluarga_asuh.taruna_id', $taruna_id)
                        ->first();
    }
}
