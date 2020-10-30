<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Content;
use App\Tags;
use App\PostCategory;
use App\Traits\ActionTable;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class ContentController extends Controller
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
        $this->middleware('permission:berita-list');
        $this->middleware('permission:berita-create', ['only' => ['create','store']]);
        $this->middleware('permission:berita-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:berita-delete', ['only' => ['destroy']]);
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
                1=>'title',
                2=>'excerpt',
                3=>'content',
                4=>'status',
                5=>'user_created',
                6=>'created_at',
                7=>'updated_at'
            );
            $model  = New Content();
            return $this->ActionTable($columns, $model, $request, 'content.edit', 'berita-edit', 'berita-delete');
        }
        return view('content.index');
    }

    public function create()
    {
        $postCategory = PostCategory::pluck('name','id')->all();
        return view('content.create', compact('postCategory'));
    }

    public function edit($id)
    {
        $postCategory = PostCategory::pluck('name','id')->all();
        $content = Content::find($id);
        $selectCategory =$content->post_categories_id;
        return view('content.edit', compact('content', 'postCategory', 'selectCategory'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|unique:posts,title',
            'excerpt' => 'required',
            'post_categories_id' => 'required',
            'content' => 'required',
            'status' => 'required',
            'file' => 'required|mimes:jpeg,bmp,png|max:300'
        ]);
        $detail=$request->input('content');
        $dom = new \DomDocument();
        $dom->loadHtml($detail, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');

        foreach($images as $k => $img){
            $data = $img->getAttribute('src');
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $image_name= '/storage/'.config('app.postImagePath').'/'. md5(Carbon::now()->format('Ymd H:i:s')).$k.'.png';
            $path = public_path() . $image_name;
            file_put_contents($path, $data);
            $img->removeAttribute('src');
            $img->setAttribute('src', $image_name);
        }

        $detail = $dom->saveHTML();

        $slug = Str::slug($request->title, '-');
        if($request->file){

            $image = $this->UploadImage($request->file, config('app.postImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('content.create');

            }
        }

       try {

            DB::beginTransaction();
                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                    }
                }
                $request->request->add(['slug'=> $slug]);
                $request->request->add(['user_created'=> Auth::user()->id]);
                $request->request->add(['created_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Arr::forget($input, array('content'));
                $input['content']=$detail;
                Content::create($input);


            DB::commit();
            \Session::flash('success','data berhasil ditambah.');
            return redirect()->route('content.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.postImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            @dd($th->getMessage());
            return redirect()->route('content.create');
        }

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|unique:posts,title,'.$id,
            'excerpt' => 'required',
            'post_categories_id' => 'required',
            'content' => 'required',
            'status' => 'required',
            'file' => 'mimes:jpeg,bmp,png|max:300'
        ]);
        $detail=$request->input('content');
        libxml_use_internal_errors(true);
        $dom = new \DomDocument();
        $dom->loadHtml($detail, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        if($images){
            foreach($images as $k => $img){
                $data = $img->getAttribute('src');
                $getData = $this->checkDataImg($data);
                if($getData != false){
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $image_name= '/storage/'.config('app.postImagePath').'/'. md5(Carbon::now()->format('Ymd H:i:s.u')).$k.'.png';
                    $path = public_path() . $image_name;
                    file_put_contents($path, $data);
                    $img->removeAttribute('src');
                    $img->setAttribute('src', $image_name);
                }
            }
        }
        $detail = $dom->saveHTML();

        $slug = Str::slug($request->title, '-');
        if($request->file){

            $image = $this->UploadImage($request->file, config('app.postImagePath'));
            if($image==false){
                \Session::flash('error', 'image upload failure');
                return redirect()->route('content.create');
            }
        }

       try {

            DB::beginTransaction();
            $content = Content::find($id);

                if(isset($image)){
                    if($image!=false){
                        $request->request->add(['photo'=> $image]);
                        $this->DeleteImage($content->file, config('app.postImagePath'));
                    }
                }
                $request->request->add(['slug'=> $slug]);
                $request->request->add(['user_updated'=> Auth::user()->id]);
                $request->request->add(['updated_at'=> date('Y-m-d H:i:s')]);
                $input = $request->all();
                Arr::forget($input, array('content'));
                $input['content']=$detail;

                $content->update($input);

            DB::commit();
            \Session::flash('success','Data berhasil diubah.');
            return redirect()->route('content.index');
        } catch (\Throwable $th) {
            DB::rollBack();
                if($image!=false){
                    $this->DeleteImage($image, config('app.postImagePath'));
                }
            \Session::flash('error','Terjadi kesalahan server');
            return redirect()->route('content.create');
        }
    }

    public function destroy($id)
    {
        $content = Content::find($id);
        try {
            DB::beginTransaction();
                $this->DeleteImage($content->photo, config('app.postImagePath'));
                $content->user_deleted = Auth::user()->id;
                $content->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return false;
        }

    }

    public function deleteExistImagePost(Request $request)
    {
        $image = $request->post('image');
        $id    = $request->post('id');

        $content = Content::find($id);
        try {
            $deleteFile = $this->DeleteImage($image, config('app.postImagePath'));
            DB::beginTransaction();
                if($deleteFile == true){
                    $input = ['photo'=>NULL];
                    $content->update($input);
                }
            DB::commit();
            return true;
        } catch (\Throwable $th) {
                $input = ['photo'=>NULL];
                $content->update($input);
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
