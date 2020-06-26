<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ActionTable;
use App\Tags;
use Auth;
use DB;

class TagsController extends Controller
{
    use ActionTable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:tags-list');
        $this->middleware('permission:tags-create', ['only' => ['create','store']]);
        $this->middleware('permission:tags-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:tags-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $columns = array(
                0=>'id',
                1=>'name',
                2=>'created_at',
                3=>'updated_at',
                4=>'user_created',
            );
            $model  = New Tags();
            return $this->ActionTable($columns, $model, $request, 'tags.edit', 'tags-edit', 'tags-delete');
        }
        return view('tags.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,
                        ['name' => 'required|unique:tags,name']
                        );
        try {
            DB::beginTransaction();
                $request->request->add(['author'=> Auth::user()->id]);
                $request->request->add(['user_created'=> Auth::user()->name]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                Tags::create($input);
            DB::commit();
            \Session::flash('success','Tags konten berhasil ditambah.');
            return redirect()->route('tags.index');

        }catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('tags.create');
        }
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
        $tags = Tags::find($id);
        return view('tags.edit',compact('tags'));
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
        $this->validate($request,
                        ['name' => 'required|unique:tags,name,'.$id]
                        );
        try {
            $tags = Tags::find($id);
            DB::beginTransaction();
                $request->request->add(['author'=> Auth::user()->id]);
                $request->request->add(['user_created'=> Auth::user()->name]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input=$request->all();
                $tags->update($input);
            DB::commit();
            \Session::flash('success','Tags berhasil diubah.');
            return redirect()->route('tags.index');

        }catch (\Throwable $th) {
            DB::rollBack();
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('tags.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Tags::find($id)->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
