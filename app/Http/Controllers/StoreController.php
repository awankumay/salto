<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Store;
use App\Traits\DataTrait;
use DataTables;
use DB;
use Auth;

class StoreController extends Controller
{
    use DataTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:store-list');
        $this->middleware('permission:store-create', ['only' => ['create','store']]);
        $this->middleware('permission:store-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:store-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Store::latest()->get();
            return $this->FetchData($data, 'store.edit', 'store-edit', 'store-delete');
        }
        return view('store.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'store_name' => 'required',
            'phone_number' => 'required',
            'address' => 'required'

        ]);

        DB::beginTransaction();
        $getCode = DB::table('stores')->latest()->sharedLock()->first();
        #@dd($getCode);
        if($getCode==null){
            $request->request->add(['store_code'=> 'ST-000']);
        }else{
            $latestCode = $getCode->store_code;
            $seq        = substr($latestCode, 3);
            $generate   = $seq+1;
            $request->request->add(['store_code'=> 'ST-'.sprintf("%03s", $generate)]);
        }
        $input=$request->all();
        $store = Store::create($input);
        \Session::put('success','store created successfully.');
        DB::commit();
        return redirect()->route('store.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $store = Store::find($id);
        return view('store.edit',compact('store'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'store_name' => 'required',
            'phone_number' => 'required',
            'address' => 'required'

        ]);

        $input = $request->all();
        $store = Store::find($id);
        $store->update($input);

        \Session::put('success','store updated successfully.');

        return redirect()->route('store.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Store::find($id)->delete()){
            return true;
        }
            return false;
    }

    public function showTable($data)
    {
        if(Auth::user()->hasPermissionTo('store-edit') && Auth::user()->hasPermissionTo('store-delete')){
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href='.route('store.edit', $row).' class="action-table text-success text-sm"><i class="fas fa-edit"></i></a> <a href="javascript:void(0)" onclick="deleteRecord('.$row->id.',this)" class="action-table text-danger text-sm"><i class="fas fa-trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }else if (Auth::user()->hasPermissionTo('store-edit')) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href='.route('store.edit', $row).' class="action-table text-success text-sm"><i class="fas fa-edit"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }else if(Auth::user()->hasPermissionTo('store-delete')){
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
