<?php

namespace App\Traits;
use Illuminate\Support\Str;
use Auth;

trait ActionTableWithDetail
{
    public function ActionTableWithDetail($columns, $model, $request, $routeEdit, $routeDetail, $permissionEdit, $permissionDelete, $permissionDetail)
    {
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        $totalData = $model->GetCount();
        $totalFiltered = $totalData;
        if(empty($request->input('search.value'))) {
            $dataModel = $model->GetCurrentData($start, $limit, $order, $dir);
        }
        else{
            $search = $request->input('search.value');
            $dataModel = $model->GetCurrentDataFilter($start, $limit, $order, $dir, $search);
            $totalFiltered = $model->GetCountDataFilter($search);
        }
        $data = array();
        if(!empty($dataModel))
        {
            foreach ($dataModel as $key => $val) {
                #$show =  route('post-category.show',$val->id);
                $detail  = '';
                $edit    = Auth::user()->hasPermissionTo($permissionEdit) ? "<a href=".route($routeEdit, $val->id)." class='action-table text-success text-sm'><i class='fas fa-edit'></i></a>" : '';
                $delete  = Auth::user()->hasPermissionTo($permissionDelete) ? "<a href='javascript:void(0)' onclick='deleteRecord(".$val->id.",this)' class='action-table text-danger text-sm'><i class='fas fa-trash'></i></a>" : '';
                if($permissionDetail!='keluarga-asuh-list'){
                    $detail  = Auth::user()->hasPermissionTo($permissionDetail) ? "<a href=".route($routeDetail, $val->id)." class='action-table text-info text-sm'><i class='fa fa-eye'></i></a>" : '';
                }else{
                    $detail  = Auth::user()->hasPermissionTo($permissionDetail) ? "<a href=".route($routeDetail, $val->id)." class='action-table text-info text-sm'><i class='fas fa-cogs'></i></a>" : '';
                }
                for ($i=0; $i < count($columns); $i++) {
                    if($columns[$i]=='created_at'){
                        $nestedData['created_at'] = date('d-m-Y H:i',strtotime($val->created_at));
                    }else if($columns[$i]=='updated_at'){
                        $nestedData['updated_at'] = $val->updated_at ? date('d-m-Y H:i',strtotime($val->updated_at)) : '';
                    }else if($columns[$i]=='deleted_at'){
                        $nestedData['deleted_at'] = $val->deleted_at ? date('d-m-Y H:i',strtotime($val->deleted_at)) : '';
                    }else if($columns[$i]=='tanggal'){
                        $nestedData['tanggal'] = $val->tanggal ? date('d-m-Y',strtotime($val->tanggal)) : '';
                    }else if($columns[$i]=='excerpt'){
                        $nestedData['excerpt'] = Str::limit($val->excerpt, 30);
                    }
                    else if($columns[$i]=='content'){
                        $nestedData['content'] = Str::limit($val->content, 30);
                    }
                    else if($columns[$i]=='title'){
                        $nestedData['title'] = Str::limit($val->title, 20);
                    }
                    else{
                        $nestedData[$columns[$i]] = $dataModel[$key][$columns[$i]];
                    }
                }
                if($routeDetail=='jurnal.show'){
                    $nestedData['action'] = "&emsp;".$detail;
                }else{
                    if($routeDetail=='surat-izin.show'){
                        if($val->user_created==Auth::user()->id || Auth::user()->getRoleNames()['0']=='Super Admin'){
                            $nestedData['action'] = "&emsp;".$delete."&emsp;".$edit."&emsp;".$detail;
                        }else{
                            $nestedData['action'] = "&emsp;".$detail;
                        }
                    }else{
                        $nestedData['action'] = "&emsp;".$delete."&emsp;".$edit."&emsp;".$detail;
                    }
                    
                }
                $data[] = $nestedData;
            }
        }
        return $json_data = array(
                             "draw"            => intval($request->input('draw')),
                             "recordsTotal"    => intval($totalData),
                             "recordsFiltered" => intval($totalFiltered),
                             "data"            => $data
                            );
    }
}
