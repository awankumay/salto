<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Image;
trait ImageTrait
{
    public $publicStorage='public/';

    public function UploadImage($data, $path, $convert=false)
    {
        $file_ext   = $data->getClientOriginalExtension();
        $file_name  = md5(Carbon::now()->format('Ymd H:i:s')) . '.' . $file_ext ;
        try {
            if($file_ext!='pdf'){
                $data->storeAs($this->publicStorage.$path, $file_name);
            }else{
                $img = Image::make($data->getRealPath());
                $img->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(public_path('/storage/').$path.'/'. $file_name, 100);
            }
           
            return $file_name;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function DeleteImage($data, $path)
    {
        try {
            @unlink(storage_path('app/'.$this->publicStorage.$path.'/'.$data));
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
