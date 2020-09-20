<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Report;
use App\PostCategory;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class ReportController extends Controller
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
        $this->middleware('permission:send-report-list');
        $this->middleware('permission:send-report-create', ['only' => ['create','store']]);
        $this->middleware('permission:send-report-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:send-report-delete', ['only' => ['destroy']]);
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
                2=>'report',
                3=>'created_at',
                4=>'updated_at'
            );
            $model  = New Report();
            return $this->ActionTable($columns, $model, $request, 'report.edit', 'send-report-edit', 'send-report-delete');
        }
        return view('send-report.index');
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

    public function destroy($id)
    {

    }
}
