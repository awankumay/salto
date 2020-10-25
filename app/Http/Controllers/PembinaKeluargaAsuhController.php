<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use App\PembinaKeluargaAsuh;
use App\KeluargaAsuh;
use App\User;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Spatie\Permission\Models\Role;
use DataTables;
use DB;
use Auth;
class PembinaKeluargaAsuhController extends Controller
{
    use ActionTable;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:data-keluarga-asuh-list');
        $this->middleware('permission:data-keluarga-asuh-create', ['only' => ['create','store']]);
        $this->middleware('permission:data-keluarga-asuh-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:data-keluarga-asuh-delete', ['only' => ['destroy']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
       
        if ($request->ajax()) {
            $columns = array(
                0=>'id',
                1=>'name',
                2=>'phone',
                3=>'whatsapp',
                4=>'created_at'
            );
            $model  = New PembinaKeluargaAsuh();
            return $this->ActionTable($columns, $model, $request, 'keluarga-asuh.edit', null, 'data-keluarga-asuh-delete');
        }
        $keluargaAsuh = KeluargaAsuh::find($id);
        return view('keluarga-asuh.show',compact('keluargaAsuh'));
    }

    public function create()
    {
    }

    public function edit($id)
    {
    }

    public function store(Request $request)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function show($id)
    {
    }

    public function destroy($id)
    {
        try {
            $data = PembinaKeluargaAsuh::find($id);
            $data->user_deleted = Auth::user()->id;
            $data->save();
            $data->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }

}
