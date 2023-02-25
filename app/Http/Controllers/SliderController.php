<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Slider;
use App\PostCategory;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class SliderController extends Controller
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
        $this->middleware('permission:banner-list');
        $this->middleware('permission:banner-create', ['only' => ['create','store']]);
        $this->middleware('permission:banner-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:banner-delete', ['only' => ['destroy']]);
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
                1=>'photo',
                2=>'name',
                3=>'status',
                4=>'created_at',
                5=>'updated_at'
            );
            $model  = New Slider();
            return $this->ActionTable($columns, $model, $request, 'slider.edit', 'banner-edit', 'banner-delete');
        }
        return view('slider.index');
    }

    public function create()
    {
        return view('slider.create');
    }

    public function edit($id)
    {
        $slider = Slider::find($id);
        return view('slider.edit', compact('slider'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
            'file' => 'required|mimes:jpeg,bmp,png,jpg|max:300'
            ]
        );

        if($request->file){
            $image = $this->UploadImage($request->file, config('app.postImagePath'));
            if($image==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('slider.create');
            }
        }

       try {

            DB::beginTransaction();
                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Slider::create($input);

            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('slider.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.postImagePath'));
                }
            }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('slider.create');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required',
            'file' => 'nullable|mimes:jpeg,bmp,png,jpg|max:300'
            ]
        );

        if($request->file){

            $image = $this->UploadImage($request->file, config('app.postImagePath'));
            if($image==false){
                \Session::flash('error', 'data upload failure');
                return redirect()->route('slider.create');
            }
        }

       try {

            DB::beginTransaction();
            $slider = Slider::find($id);

                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                        $this->DeleteImage($slider->photo, config('app.postImagePath'));
                    }
                }
                $input = $request->all();
                $slider->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diubah.');
            return redirect()->route('slider.index');
        } catch (\Throwable $th) {
            DB::rollBack();
            if(isset($image)){
                if($image!=false){
                    $this->DeleteImage($image, config('app.postImagePath'));
                }
            }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('slider.index');
        }
    }

    public function destroy($id)
    {
        $slider = Slider::find($id);
        try {
            DB::beginTransaction();
            if($slider->photo){
                $this->DeleteImage($slider->photo, config('app.postImagePath'));
            }
            $slider->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImageSlider(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');
        $deleteFile = false;
        $slider = Slider::find($id);
        try {
            if(!empty($image)){
                $deleteFile = $this->DeleteImage($image, config('app.postImagePath'));
            }
            DB::beginTransaction();
                if($deleteFile == true || !empty($image)){
                    $input = ['photo'=>NULL];
                    $slider->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            if($image){
                $input = ['photo'=>NULL];
                $slider->update($input);
            }
            DB::rollback();
            return false;
        }
    }
}
