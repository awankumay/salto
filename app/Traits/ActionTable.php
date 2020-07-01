<?php

namespace App\Traits;
use Illuminate\Support\Str;
use Auth;

trait ActionTable
{
    public function ActionTable($columns, $model, $request, $routeEdit, $permissionEdit, $permissionDelete)
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
                $edit    = Auth::user()->hasPermissionTo($permissionEdit) ? "<a href=".route($routeEdit, $val->id)." class='action-table text-success text-sm'><i class='fas fa-edit'></i></a>" : '';
                $delete  = Auth::user()->hasPermissionTo($permissionDelete) ? "<a href='javascript:void(0)' onclick='deleteRecord(".$val->id.",this)' class='action-table text-danger text-sm'><i class='fas fa-trash'></i></a>" : '';
                for ($i=0; $i < count($columns); $i++) {
                    if($columns[$i]=='created_at'){
                        $nestedData['created_at'] = date('d-m-Y H:i',strtotime($val->created_at));
                    }else if($columns[$i]=='updated_at'){
                        $nestedData['updated_at'] = $val->updated_at ? date('d-m-Y H:i',strtotime($val->updated_at)) : '';
                    }else if($columns[$i]=='deleted_at'){
                        $nestedData['deleted_at'] = $val->deleted_at ? date('d-m-Y H:i',strtotime($val->deleted_at)) : '';
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
                $nestedData['action'] = "&emsp;".$delete."&emsp;".$edit;
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
