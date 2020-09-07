<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Convict;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class ConvictController extends Controller
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
        $this->middleware('permission:convict-list');
        $this->middleware('permission:convict-create', ['only' => ['create','store']]);
        $this->middleware('permission:convict-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:convict-delete', ['only' => ['destroy']]);
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
                2=>'type_convict',
                3=>'violation',
                5=>'lockup',
                7=>'created_at',
                8=>'updated_at'
            );
            $model  = New Convict();
            return $this->ActionTable($columns, $model, $request, 'convict.edit', 'convict-edit', 'convict-delete');
        }
        return view('convict.index');
    }

    public function create()
    {
        $type_convict=['1'=>'Tahanan Baru',
                       '2'=>'Narapidana'];
        $block=['A1'=>'A1',
               'A1'=>'A2',
               'A3'=>'A3'];
        $lockup=['A1-BARAK-KIRI'=>'A1-BARAK-KIRI', 'A2-BARAK-KIRI'=>'A2-BARAK-KIRI', 'A3-BARAK-KIRI'=>'A3-BARAK-KIRI', 'LAINNYA'=>'LAINNYA'];
        $violation=['Penipuan', 'Pembunuhan', 'Narkotika', 'DLL'];
        return view('convict.create', compact('type_convict', 'block', 'lockup', 'violation'));
    }

    public function edit($id)
    {
        $type_convict = ['1'=>'Tahanan Baru', '2'=>'Narapidana'];
        $convict = Convict::find($id);
        return view('content.edit', compact('convict', 'type_convict'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:convicts,name',
            'type_convict' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:300',
            'file_2' => 'nullable|mimes:jpeg,bmp,png,jpg,pdf|max:500'
            ],
            ['type_convict.required'=>'pilih kategori'],
        );

        if($request->file){

            $image = $this->UploadImage($request->file, config('app.convictImagePath'));
            if($image==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('convict.create');

            }
        }

        if($request->file_2){

            $document = $this->UploadImage($request->file_2, config('app.documentImagePath'));
            if($document==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('convict.create');

            }
        }

       try {

            DB::beginTransaction();
                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                if(isset($document)){
                    if($document!=false){
                        $request->request->add(['document'=> $document]);
                    }
                }
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Convict::create($input);


            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('convict.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.convictImagePath'));
                }
            }
            if(isset($document)){
                if($document!=false){
                    $this->DeleteImage($document, config('app.documentImagePath'));
                }
            }
            @dd($th->getMessage());
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('convict.create');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:convicts,name,'.$id,
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:300',
            'file_2' => 'nullable|mimes:jpeg,bmp,png,jpg,pdf|max:500'
        ]);

        if($request->file){

            $image = $this->UploadImage($request->file, config('app.convictImagePath'));
            if($image==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('content.create');
            }
        }

        if($request->file_2){

            $document = $this->UploadImage($request->file_2, config('app.documentImagePath'));
            if($document==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('convict.create');
            }
        }

       try {

            DB::beginTransaction();
            $convict = Convict::find($id);

                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                        $this->DeleteImage($convict->photo, config('app.convictImagePath'));
                    }
                }
                if(isset($document)){
                    if($document!=false){
                        $request->request->add(['document'=> $document]);
                        $this->DeleteImage($convict->document, config('app.documentImagePath'));
                    }
                }
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                $convict->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diubah.');
            return redirect()->route('convict.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.convictImagePath'));
                }
                if($document!=false){
                    $this->DeleteImage($document, config('app.convictImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('convict.create');
        }
    }

    public function destroy($id)
    {
        $convict = Convict::find($id);
        try {
            DB::beginTransaction();
            if($convict->photo){
                $this->DeleteImage($convict->photo, config('app.convictImagePath'));
            }
            if($convict->document){
                $this->DeleteImage($convict->document, config('app.documentImagePath'));
            }
            $convict->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImageConvict(Request $request)
    {
        $image = $request->post('image');
        $document = $request->post('document');
        $id    = $request->post('id');

        $convict = Convict::find($id);
        try {
            if(!empty($image)){
                $deleteFile = $this->DeleteImage($image, config('app.convictImagePath'));
            }
            if(!empty($document)){
                $deleteFile2 = $this->DeleteImage($document, config('app.documentImagePath'));
            }
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL];
                    $convict->update($input);
                }
                if($deleteFile2 == true){
                    $input = ['document'=>NULL];
                    $convict->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            if($image){
                $input = ['photo'=>NULL];
                $convict->update($input);
            }
            if($document){
                $input = ['document'=>NULL];
                $convict->update($input);
            }
            DB::rollback();
            return false;
        }
    }

    public function checkDataImg($data)
    {
        try {
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            return true;
        } catch (\Throwable $th) {
            return false;
        }

    }
}
