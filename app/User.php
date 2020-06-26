<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
#use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    #use Notifiable;
    use HasRoles;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'whatsapp', 'address', 'description', 'tagline', 'sex', 'status', 'photo', 'user_type'
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

    public function PostCategory(){
    	return $this->hasMany(PostCategory::class, 'author', 'id');
    }
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
                            ->count();
        return $data;
    }
}
