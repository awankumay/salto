<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

trait ImageTrait
{
    public $publicStorage='public/';

    public function UploadImage($data, $path)
    {
        $file_ext   = $data->getClientOriginalExtension();
        $file_name  = md5(Carbon::now()->format('Ymd H:i:s')) . '.' . $file_ext ;
        try {
            $data->storeAs($this->publicStorage.$path, $file_name);
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
