<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\User;
use App\SuratIzin;
use App\IzinSakit;
use App\KeluarKampus;
use App\TrainingCenter;
use App\PernikahanSaudara;
use App\PemakamanKeluarga;
use App\OrangTuaSakit;
use App\KegiatanDalam;
use App\Tugas;
use App\KegiatanPesiar;
use App\Permission;
use App\Traits\ActionTableWithDetail;
use App\Traits\ImageTrait;
use Hash;
use DataTables;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use Carbon\Carbon;

class CetakSuratController extends Controller
{
    use ActionTableWithDetail;
    use ImageTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function cetaksurat(Request $request)
    {
        if(!empty($request->hash)){
            $table = DB::table('tmp_report')->where('hash', $request['hash'])->first();
            if(!empty($table)){
                $params = json_decode($table->params);
                if($params->cetak=='perizinan'){
                    $data = $this->datasuratizin($params);
                    if(!empty($data)){
                        $pdf = app()->make('dompdf.wrapper');
                        $pdf->loadView('cetaksurat', compact('data'))->setPaper('a4', 'portrait');
                        return $pdf->stream();
                        /* $content = $pdf->download()->getOriginalContent();
                        $name = \Str::slug($data['category_name'].'-'.$data['name'].'-'.date('dmyhis')).".pdf";
                        Storage::put('public/'.config('app.documentImagePath').'/temp/'.$name, $content) ;
                       
                        //\Storage::put(config('app.documentImagePath').$name, $pdf->output());
                        //$data->storeAs('public/'.config('app.documentImagePath'), $file_name);
                        $link =  \URL::to('/').'/storage/'.config('app.documentImagePath').'/temp/'.$name;
                        // */
                    }
                }
            }
        }
    }


    public function datasuratizin($request)
    {
        $id   = $request->id;
        $getSurat = SuratIzin::join('users as author', 'author.id', '=', 'surat_header.id_user')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'surat_header.user_approve_level_1')
                                    ->leftjoin('users as user_approve_2', 'user_approve_2.id', '=', 'surat_header.user_approve_level_2')
                                    ->leftjoin('users as user_disposisi', 'user_disposisi.id', '=', 'surat_header.user_disposisi')
                                    ->select('surat_header.id as id', 
                                            'surat_header.id_user as id_user',
                                            'author.name as nama_taruna',
                                            'surat_header.photo as photo',
                                            'surat_header.id_category as id_category',
                                            'surat_header.status as status',
                                            'surat_header.start as start',
                                            'surat_header.end as end',
                                            'user_approve_1.name as user_approve_1',
                                            'surat_header.date_approve_level_1 as date_approve_1',
                                            'surat_header.reason_level_1 as user_reason_1',
                                            'user_approve_2.name as user_approve_2',
                                            'surat_header.date_approve_level_2 as date_approve_2',
                                            'surat_header.reason_level_2 as user_reason_2',
                                            'user_disposisi.name as user_disposisi',
                                            'surat_header.date_disposisi as date_disposisi',
                                            'surat_header.status_disposisi as status_disposisi',
                                            'surat_header.reason_disposisi as reason_disposisi'
                                            )
                                    ->where('surat_header.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $data;
        }
        $getCategory = Permission::where('id', $getSurat->id_category)->first();
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $author = User::find($getSurat->id_user);
        $permission = [];
        foreach ($getUser->getAllPermissions() as $key => $vals) {
            $permission[]=$vals->name;
        }
        if($getSurat->status_disposisi==1){
            $status_disposisi = 'Disposisi';
        }else if ($getSurat->status_disposisi==0) {
            $status_disposisi = 'Belum Disposisi';
        }else {
            $status_disposisi = 'Disposisi Ditolak';
        }
        switch ($getSurat->id_category) {
            case 1:
                $getSuratDetail = IzinSakit::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'id_category'=>$getSurat->id_category,
                        'name'=>$author->name,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'header'=>['No', 'Nama', 'STB', 'Keluhan', 'Diagnosa', 'Rekomendasi', 'Dokter', 'Tanggal'],
                        'body'=>['1', $author->name, $author->stb, $getSuratDetail->keluhan, $getSuratDetail->diagnosa, 
                                    $getSuratDetail->rekomendasi, $getSuratDetail->dokter, date_format(date_create($getSurat->start), 'd-m-Y H:i').' sd '.date_format(date_create($getSurat->end), 'd-m-Y H:i')],
                        'template'=>1
                    );
                }

                break;
            case 2:
                $getSuratDetail = KeluarKampus::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'status'=>$getSurat->status,
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'header'=>['No', 'Nama', 'STB', 'Keperluan', 'Jam Mulai', 'Jam Akhir', 'Pendamping', 'Tanggal'],
                        'body'=>['1', $author->name, $author->stb, $getSuratDetail->keperluan, date_format(date_create($getSurat->start), 'H:i'), date_format(date_create($getSurat->end), 'H:i'), $getSuratDetail->pendamping, 
                                   date_format(date_create($getSurat->created_at), 'd-m-Y H:i')],
                        'template'=>1
                    );
                }
                break;
            case 3:
                $getSuratDetail = TrainingCenter::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'status'=>$getSurat->status,
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'header'=>['No', 'Nama', 'STB', 'Training Center', 'Jam Mulai', 'Jam Akhir', 'Pelatih', 'Tanggal'],
                        'body'=>['1', $author->name, $author->stb, $getSuratDetail->nm_tc, date_format(date_create($getSurat->start), 'H:i'), date_format(date_create($getSurat->end), 'H:i'), $getSuratDetail->pelatih, 
                                   date_format(date_create($getSurat->created_at), 'd-m-Y H:i')],
                        'template'=>1
                     
                    );
                }
                break;
            case 4:
                $getSuratDetail = PernikahanSaudara::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'No.STB', 'Keperluan', 'Tujuan', 'Tanggal Awal', 'Tanggal Akhir'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->Keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y'), date_format(date_create($getSurat->end), 'd-m-Y')],
                        'template'=>2,
                        'id_surat_cetak'=>$getSurat->id+1
                    );
                }
                break;
            case 5:
                $getSuratDetail = PemakamanKeluarga::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'No.STB', 'Keperluan', 'Tujuan', 'Tanggal Awal', 'Tanggal Akhir'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->Keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y'), date_format(date_create($getSurat->end), 'd-m-Y')],
                        'template'=>2,
                        'id_surat_cetak'=>$getSurat->id+1
                    );
                }
                break;
            case 6:
                $getSuratDetail = OrangTuaSakit::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'No.STB', 'Keperluan', 'Tujuan', 'Tanggal Awal', 'Tanggal Akhir'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->Keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y'), date_format(date_create($getSurat->end), 'd-m-Y')],
                        'template'=>2,
                        'id_surat_cetak'=>$getSurat->id+1
                    );
                }
                break;
            case 7:
                $getSuratDetail = Tugas::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'STB', 'Keperluan', 'Tujuan', 'Mulai', 'Akhir', 'Tanggal Pengajuan'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y H:i'), date_format(date_create($getSurat->end), 'd-m-Y H:i'), $getSurat->created_at],
                        'template'=>1
                    );
                }
                break;
            case 8:
                $getSuratDetail = KegiatanDalam::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'STB', 'Keperluan', 'Tujuan', 'Mulai', 'Akhir', 'Tanggal Pengajuan'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y H:i'), date_format(date_create($getSurat->end), 'd-m-Y H:i'), $getSurat->created_at],
                        'template'=>1
                    );
                }
                break;
            case 9:
                $getSuratDetail = KegiatanPesiar::where('id_surat', $id)->where('id_user', $getSurat->id_user)->first();
                if(!empty($getSurat) && !empty($getSuratDetail)){
                    $data = array(
                        'name'=>$author->name,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'STB', 'Keperluan', 'Tujuan', 'Mulai', 'Akhir', 'Tanggal Pengajuan'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y H:i'), date_format(date_create($getSurat->end), 'd-m-Y H:i'), $getSurat->created_at],
                        'template'=>1
         
                    );
                }
                break;
            default:
                $getSuratDetail = [];
                break;
        }
        if(strtotime(date_format(date_create($getSurat->end), 'Y-m-d')) > strtotime(date_format(date_create($getSurat->start), 'Y-m-d'))){
            if(in_array($data['id_category'], ['1', '4', '5', '6', '9'])){
                $data['user_approve_2']=$getSurat->user_approve_2;
                $data['date_approve_2']=$getSurat->date_approve_2;
                $data['user_reason_2']=$getSurat->user_reason_2;
                $data['menginap']='Izin Menginap';
                if($roleName=='Direktur' && $data['status']!=1 && $data['status_level_1']==1){
                    $data['show_persetujuan'] = true;
                }
            }
        }
        return $data;
    }
}
