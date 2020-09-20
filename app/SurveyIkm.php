<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class SurveyIkm extends Model
{
    protected $table = 'ratings';
    protected $fillable = [
        'users_id', 'rating'
    ];
    protected $primaryKey = 'id';

    public function GetCount()
    {
        return SurveyIkm::count();
    }

    public function GetCurrentData($start, $limit, $order, $dir)
    {
        $data = SurveyIkm::select(DB::raw("ratings.id as id, users.name as name, ratings.rating, ratings.created_at, ratings.updated_at"))
                            ->leftJoin('users', 'users.id', '=', 'ratings.users_id')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('ratings.'.$order,$dir)
                            ->get();
        return $data;
    }

    public function GetCurrentDataFilter($start, $limit, $order, $dir, $search)
    {
        $data = SurveyIkm::where('ratings.id','LIKE',"%{$search}%")
                            ->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("ratings.id as id, users.name as name, ratings.rating, ratings.created_at, ratings.updated_at"))
                            ->leftJoin('users', 'users.id', '=', 'ratings.users_id')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy('ratings.'.$order,$dir)
                            ->get();
        return $data;
    }

    public function GetCountDataFilter($search){
        $data = SurveyIkm::where('ratings.id','LIKE',"%{$search}%")->orWhere('users.name', 'LIKE', "%{$search}%")
                            ->select(DB::raw("ratings.id as id, users.name as name, ratings.rating, ratings.created_at, ratings.updated_at"))
                            ->leftJoin('users', 'users.id', '=', 'ratings.users_id')
                            ->count();
        return $data;
    }
}
