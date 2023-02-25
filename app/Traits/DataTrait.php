<?php

namespace App\Traits;

use Illuminate\Http\Request;
use DataTables;
use Auth;

trait DataTrait
{
    public function FetchData($data, $routeEdit, $permissionEdit, $permissionDelete)
    {
        if(Auth::user()->hasPermissionTo($permissionEdit) && Auth::user()->hasPermissionTo($permissionDelete)){
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) use ($routeEdit){
                    $btn = '<a href='.route($routeEdit, $row).' class="action-table text-success text-sm"><i class="fas fa-edit"></i></a> <a href="javascript:void(0)" onclick="deleteRecord('.$row->id.',this)" class="action-table text-danger text-sm"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }else if (Auth::user()->hasPermissionTo($permissionEdit)) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) use ($routeEdit){
                    $btn = '<a href='.route($routeEdit, $row).' class="action-table text-success text-sm"><i class="fas fa-edit"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }else if(Auth::user()->hasPermissionTo($permissionDelete)){
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" onclick="deleteRecord('.$row->id.',this)" class="action-table text-danger text-sm"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }else{
            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '';
                return $btn;
            })
            ->make(true);
        }
    }
}
