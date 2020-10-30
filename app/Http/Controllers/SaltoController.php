<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Regencies;
use App\User;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class SaltoController extends Controller
{
    use ActionTable;
    use ImageTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getregencies(Request $request)
    {
        if ($request->ajax()) {
            $city = [];
            $data = Regencies::where('province_id', $request->province_id)->get();
            foreach ($data as $key => $value) {
                $city[] = ['id'=>$value['id'], 'text'=>$value['name']];
            }
            echo json_encode($city);
            return;
        }
    }

    public function editprofile()
    {
        return view('salto.profile');
    }

    public function gettaruna(Request $request)
    {
        $search =$request->get('search');
        $getTaruna = User::role('Taruna')->where('name', 'like', "%$search%")->get();
        
        if (!empty($getTaruna)) {
            $list = array();
            foreach ($getTaruna as $key => $row) {
                $list[$key]['id'] = $row->id;
                $list[$key]['text'] = $row->name; 
            }
            echo json_encode($list);
        } else {
            echo "hasil kosong";
        }
    }
}
