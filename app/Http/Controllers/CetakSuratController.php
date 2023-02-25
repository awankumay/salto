<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\User;
use App\Grade;
use App\SuratIzin;
use App\IzinSakit;
use App\Prestasi;
use App\Suket;
use App\HukumanDinas;
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
                    }
                }else if($params->cetak=='hukdis'){
                    $getData    = $this->hukdisdetail($params);
                    $data       = array(
                                    'name'=>$getData['nama_taruna'],
                                    'no_stb'=>$getData['stb'],
                                    'category_name'=>'DATA HUKUMAN DISIPILIN',
                                    'tanggal_cetak'=>\Carbon\Carbon::parse($getData['date_approve_1'])->isoFormat('D MMMM Y'),
                                    'user_approve_1' =>$getData['user_approve_1'],
                                    'date_approve_1' =>$getData['date_approve_1'],
                                    'header'=>['No', 'Nama', 'No.STB', 'Keterangan', 'Tingkat', 'Hukuman', 'Waktu', 'TGL Pengajuan'],
                                    'body'=>['1', $getData['nama_taruna'], $getData['stb'], $getData['keterangan'], $getData['tingkat_name'], $getData['hukuman'], $getData['start_time_bi'].' sd '.$getData['end_time_bi'], $getData['created_at_bi']],
                                    'template'=>1
                                );
                    if(!empty($getData)){
                        $pdf = app()->make('dompdf.wrapper');
                        $pdf->loadView('cetaksurat', compact('data'))->setPaper('a4', 'portrait');
                        return $pdf->stream();
                    }
                }else if($params->cetak=='prestasi'){
                    $getData    = $this->prestasidetail($params);
                    $data       = array(
                        'name'=>$getData['name'],
                        'category_name'=>'DATA PENGHARGAAN',
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getData['date_approve_1'])->isoFormat('D MMMM Y'),
                        'user_approve_1' =>$getData['user_approve_1'],
                        'date_approve_1' =>$getData['date_approve_1'],
                        'header'=>['No', 'Nama', 'No.STB', 'Keterangan Penghargaan', 'Tingkat', 'Tempat', 'Waktu', 'Tanggal Pengajuan'],
                        'body'=>['1', $getData['name'], $getData['stb'], $getData['keterangan'], $getData['tingkat'], $getData['tempat'], $getData['waktu'], $getData['created_at_bi']],
                        'template'=>1
                    );
                    if(!empty($getData)){
                        $pdf = app()->make('dompdf.wrapper');
                        $pdf->loadView('cetaksurat', compact('data'))->setPaper('a4', 'portrait');
                        return $pdf->stream();
                    }
                }else if($params->cetak=='suket'){
                    $getData   = $this->suketdetail($params);
                    $data   = array(
                        'name'=>$getData['name'],
                        'category_name'=>'SURAT KETERANGAN',
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getData['date_approve_2'])->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'No.STB', 'Tempat, Tanggal Lahir', 'Anak Dari : ', 
                                    'Nama Orang Tua', 'Pekerjaan', 'Alamat'],
                        'body'=>[$getData['name'], $getData['stb'], $getData['ttl'], '', $getData['orangtua'], $getData['pekerjaan'], $getData['alamat']],
                        'template'=>3,
                        'id_surat_cetak'=>$getData['id']+1
                    );

                    if(!empty($getData)){
                        $pdf = app()->make('dompdf.wrapper');
                        $pdf->loadView('cetaksurat', compact('data'))->setPaper('a4', 'portrait');
                        return $pdf->stream();
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
        $getGrade = Grade::join('users','grade_table.id','=','users.grade')
        ->select('users.name','users.stb','grade_table.grade')
        ->where('users.id','=',$getUser->id)
        ->first();
        // dd($getGrade->grade);
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
                        'header_keperluan'=>'',
                        'header_tujuan'=>'',
                        'header_diagnosa'=>'Diagnosa',
                        'header_keluhan'=>'Keluhan',
                        // 'header_rekomendasi'=>'Rekomendasi',
                        'header_dokter'=>'Dokter',
                        'header_traning'=>'',
                        'header_pelatih'=>'',
                        'name'=>$author->name,
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keluhan'=>$getSuratDetail->keluhan,
                        'diagnosa'=>$getSuratDetail->diagnosa,
                        'rekomendasi'=>$getSuratDetail->rekomendasi,
                        'dokter'=>$getSuratDetail->dokter,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'id_category'=>$getSurat->id_category,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>!empty($getSurat->date_approve_2) ? \Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($getSurat->date_approve_1)->isoFormat('D MMMM Y'),
                        'user_approve_1' =>$getSurat->user_approve_1,
                        'date_approve_1' =>$getSurat->date_approve_1,
                        'user_disposisi'=>$getSurat->user_disposisi,
                        'date_disposisi'=>$getSurat->date_disposisi,
                        'header'=>['Nama', 'STB', 'Keluhan', 'Diagnosa', 'Rekomendasi', 'Dokter', 'Tanggal'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->keluhan, $getSuratDetail->diagnosa, 
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
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keluhan'=>'',
                        'diagnosa'=>'',
                        'header_keperluan'=>'Keperluan',
                        'header_tujuan'=>'',
                        'header_diagnosa'=>'Diagnosa',
                        'header_keluhan'=>'Keluhan',
                        'header_dokter'=>'',
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
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
                        'keluhan'=>'',
                        'diagnosa'=>'',
                        'header_keperluan'=>'',
                        'header_tujuan'=>'',
                        'header_diagnosa'=>'',
                        'header_keluhan'=>'',
                        'header_dokter'=>'',
                        'training'=>$getSuratDetail->nm_tc,
                        'pelatih'=>$getSuratDetail->pelatih,
                        'name'=>$author->name,
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
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
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
                        'id_category'=>$getSurat->id_category,
                        'created_at'=>$getSurat->created_at,
                        'category_name'=>'SURAT '.strtoupper($getCategory->nama_menu),
                        'tanggal_cetak'=>\Carbon\Carbon::parse($getSurat->date_approve_2)->isoFormat('D MMMM Y'),
                        'header'=>['Nama', 'No.STB', 'Keperluan', 'Tujuan', 'Tanggal Awal', 'Tanggal Akhir'],
                        'body'=>[$author->name, $author->stb, $getSuratDetail->keperluan, $getSuratDetail->tujuan, date_format(date_create($getSurat->start), 'd-m-Y'), date_format(date_create($getSurat->end), 'd-m-Y')],
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
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
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
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
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
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
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
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
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
                        'no_stb'=>$author->stb,
                        'grade'=>$author->grade,
                        'keluhan'=>'',
                        'diagnosa'=>'',
                        'header_keperluan'=>'Keperluan',
                        'header_tujuan'=>'Tujuan',
                        'header_diagnosa'=>'Diagnosa',
                        'header_keluhan'=>'Keluhan',
                        'header_dokter'=>'',
                        'keperluan'=>$getSuratDetail->keperluan,
                        'tujuan'=>$getSuratDetail->tujuan,
                        'grade'=>$getGrade->grade,
                        'start'=>$getSurat->start,
                        'end'=>$getSurat->end,
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

    public function hukdisdetail($request)
    {
        $id   = $request->id;
        $getSurat = HukumanDinas::join('users as taruna', 'taruna.id', '=', 'tb_hukdis.id_taruna')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'tb_hukdis.user_approve_level_1')
                                    ->leftjoin('users as pembina', 'pembina.id', '=', 'tb_hukdis.id_user')
                                    ->leftjoin('grade_table as grade', 'grade.id', '=', 'tb_hukdis.grade')
                                    ->select('tb_hukdis.id as id', 
                                            'tb_hukdis.id_user as id_user',
                                            'tb_hukdis.id_taruna as id_taruna',
                                            'tb_hukdis.stb as stb',
                                            'taruna.name as nama_taruna',
                                            'pembina.name as nama_pembina',
                                            'tb_hukdis.photo as photo',
                                            'tb_hukdis.keterangan as keterangan',
                                            'tb_hukdis.tingkat as tingkat',
                                            'tb_hukdis.hukuman as hukuman',
                                            'tb_hukdis.start_time as start_time',
                                            'tb_hukdis.end_time as end_time',
                                            'tb_hukdis.status as status',
                                            'tb_hukdis.updated_at as updated_at',
                                            'user_approve_1.name as user_approve_1',
                                            'tb_hukdis.date_approve_level_1 as date_approve_1',
                                            'tb_hukdis.reason_level_1 as user_reason_1',
                                            'tb_hukdis.status_level_1 as status_level_1',
                                            'grade.grade as grade'
                                            )
                                    ->where('tb_hukdis.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseError($data, 'Hukuman Dinas Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];

        switch ($getSurat->tingkat) {
            case 1:
                $tingkat = 'Ringan';
                break;
            case 2:
                $tingkat = 'Sedang';
                break;
            case 2:
                $tingkat = 'Berat';
                break;
            
            default:
                $tingkat = 'Ringan';
                break;
        }

        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'id_taruna'=>$getSurat->id_taruna,
            'stb'=>$getSurat->stb,
            'nama_taruna'=>$getSurat->nama_taruna,
            'grade'=>$getSurat->grade,
            'keterangan'=>$getSurat->keterangan,
            'tingkat'=>$getSurat->tingkat,
            'tingkat_name'=>$tingkat,
            'hukuman'=>$getSurat->hukuman,
            'start_time'=>date('Y-m-d H:i', strtotime($getSurat->start_time)),
            'end_time'=>date('Y-m-d H:i', strtotime($getSurat->end_time)),
            'start_time_bi'=>date('d-m-Y H:i', strtotime($getSurat->start_time)),
            'end_time_bi'=>date('d-m-Y H:i', strtotime($getSurat->end_time)),
            'nama_pembina'=>$getSurat->nama_pembina,
            'created_at'=>date('Y-m-d H:i', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y H:i', strtotime($getSurat->updated_at)),
            'status'=>$getSurat->status,
            'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/hukdis/".$getSurat->photo : '',
            'form'=>['keterangan', 'tingkat', 'hukuman', 'id_taruna', 'start_time', 'end_time', 'id_user'],
            'user_approve_1'=>$getSurat->user_approve_1,
            'date_approve_1'=>$getSurat->date_approve_1,
            'status_level_1'=>$getSurat->status_level_1,
            'reason_level_1'=>$getSurat->user_reason_1,
            'show_persetujuan'=>false,
            'download'=>'-'
        );
        if(!empty($request->cetak)){
            return $data;
        }

        if($getSurat->status==1){
            $data['status_name'] = 'Disetujui';
        }else if ($getSurat->status==0) {
            $data['status_name'] = 'Belum Disetujui';
        }else {
            $data['status_name'] = 'Tidak Disetujui';
        }
        $data['permission'] = [];
        if($roleName=='Pembina' && $getSurat->status_level_1!=1 && $getSurat->status!=1){
            $data['permission'] = ['edit', 'delete'];
        }
        if(($roleName=='Akademik dan Ketarunaan' || $roleName=='Super Admin') && $getSurat->status!=1){
            $data['show_persetujuan'] = true;
        }
        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/api/cetakhukdis/id/'.$request->id.'/id_user/'.$request->id_user;
        }
    }

    public function prestasidetail($request)
    {
        $id   = $request->id;
        $getSurat = Prestasi::join('users as author', 'author.id', '=', 'tb_penghargaan.id_user')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'tb_penghargaan.user_approve_level_1')
                                    ->leftjoin('users as user_disposisi', 'user_disposisi.id', '=', 'tb_penghargaan.user_disposisi')
                                    ->leftjoin('grade_table as grade', 'grade.id', '=', 'tb_penghargaan.grade')
                                    ->select('tb_penghargaan.id as id', 
                                            'tb_penghargaan.id_user as id_user',
                                            'tb_penghargaan.stb as stb',
                                            'author.name as nama_taruna',
                                            'tb_penghargaan.photo as photo',
                                            'tb_penghargaan.keterangan as keterangan',
                                            'tb_penghargaan.tingkat as tingkat',
                                            'tb_penghargaan.tempat as tempat',
                                            'tb_penghargaan.waktu as waktu',
                                            'tb_penghargaan.status as status',
                                            'tb_penghargaan.updated_at as updated_at',
                                            'user_approve_1.name as user_approve_1',
                                            'tb_penghargaan.date_approve_level_1 as date_approve_1',
                                            'tb_penghargaan.reason_level_1 as user_reason_1',
                                            'tb_penghargaan.status_level_1 as status_level_1',
                                            'user_disposisi.name as user_disposisi',
                                            'tb_penghargaan.date_disposisi as date_disposisi',
                                            'tb_penghargaan.status_disposisi as status_disposisi',
                                            'tb_penghargaan.reason_disposisi as reason_disposisi',
                                            'grade.grade as grade'
                                            )
                                    ->where('tb_penghargaan.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseFalse($data, 'Penghargaan Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'stb'=>$getSurat->stb,
            'name'=>$getSurat->nama_taruna,
            'grade'=>$getSurat->grade,
            'keterangan'=>$getSurat->keterangan,
            'tingkat'=>$getSurat->tingkat,
            'tempat'=>$getSurat->tempat,
            'waktu'=>$getSurat->waktu,
            'created_at'=>date('Y-m-d', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y', strtotime($getSurat->updated_at)),
            'status'=>$getSurat->status,
            'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/prestasi/".$getSurat->photo : '',
            'form'=>['keterangan', 'tingkat', 'tempat', 'waktu'],
            'status_disposisi'=> $getSurat->status_disposisi,
            'user_disposisi'=>$getSurat->user_disposisi,
            'date_disposisi'=>$getSurat->date_disposisi,
            'reason_disposisi'=>$getSurat->reason_disposisi,
            'user_approve_1'=>$getSurat->user_approve_1,
            'date_approve_1'=>$getSurat->date_approve_1,
            'status_level_1'=>$getSurat->status_level_1,
            'reason_level_1'=>$getSurat->user_reason_1,
            'show_disposisi'=>false,
            'show_approve'=>false,
            'download'=>false
        );
        if(!empty($request->cetak)){
            return $data;
        }
        if($getSurat->status_disposisi==1){
            $status_disposisi = 'Disposisi';
        }else if ($getSurat->status_disposisi==0) {
            $status_disposisi = 'Belum Disposisi';
        }else {
            $status_disposisi = 'Disposisi Ditolak';
        }

        if($getSurat->status==1){
            $data['status_name'] = 'Disetujui';
        }else if ($getSurat->status==0) {
            $data['status_name'] = 'Belum Disetujui';
        }else {
            $data['status_name'] = 'Tidak Disetujui';
        }
    
        if($roleName=='Pembina' && $getSurat->status_level_1!=1 && $getSurat->status!=1){
            $data['show_disposisi'] = true;
        }
        $data['permission'] = [];
        if(($roleName=='Taruna')) {
            if($getSurat->id_user==$request->id_user && $getSurat->status_disposisi!=1 && $getSurat->status!=1){
                $data['permission'] = ['edit', 'delete'];
            }
        }
        if(($roleName=='Akademik dan Ketarunaan' || $roleName=='Super Admin') && $getSurat->status!=1 && $getSurat->status_disposisi==1){
            $data['show_approve'] = true;
        }
        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/api/cetaksurat/id/'.$request->id.'/id_user/'.$request->id_user.'/cetak/prestasi';
        }

        return $this->sendResponse($data, 'prestasi load successfully.');
    }

    public function suketdetail($request)
    {
        $id   = $request->id;
        $getSurat = Suket::join('users as author', 'author.id', '=', 'tb_suket.id_user')
                                    ->leftjoin('users as user_approve_1', 'user_approve_1.id', '=', 'tb_suket.user_approve_level_1')
                                    ->leftjoin('users as user_approve_2', 'user_approve_2.id', '=', 'tb_suket.user_approve_level_2')
                                    ->leftjoin('users as user_disposisi', 'user_disposisi.id', '=', 'tb_suket.user_disposisi')
                                    ->leftjoin('grade_table as grade', 'grade.id', '=', 'tb_suket.grade')
                                    ->select('tb_suket.id as id', 
                                            'tb_suket.id_user as id_user',
                                            'tb_suket.stb as stb',
                                            'author.name as nama_taruna',
                                            'tb_suket.photo as photo',
                                            'tb_suket.ttl as ttl',
                                            'tb_suket.orangtua as orangtua',
                                            'tb_suket.pekerjaan as pekerjaan',
                                            'tb_suket.status as status',
                                            'tb_suket.alamat as alamat',
                                            'tb_suket.keperluan as keperluan',
                                            'tb_suket.updated_at as updated_at',
                                            'user_approve_1.name as user_approve_1',
                                            'tb_suket.date_approve_level_1 as date_approve_1',
                                            'tb_suket.reason_level_1 as user_reason_1',
                                            'tb_suket.status_level_1 as status_level_1',
                                            'user_approve_2.name as user_approve_2',
                                            'tb_suket.date_approve_level_2 as date_approve_2',
                                            'tb_suket.reason_level_2 as user_reason_2',
                                            'tb_suket.status_level_2 as status_level_2',
                                            'user_disposisi.name as user_disposisi',
                                            'tb_suket.date_disposisi as date_disposisi',
                                            'tb_suket.status_disposisi as status_disposisi',
                                            'tb_suket.reason_disposisi as reason_disposisi',
                                            'tb_suket.user_created as user_created',
                                            'grade.grade as grade'
                                            )
                                    ->where('tb_suket.id', $id)
                                    ->first();
        $data = [];
        if(empty($getSurat)){
            return $this->sendResponseError($data, 'Suket Not Found or Deleted');
        }
        $getUser = User::find($request->id_user);
        $roleName = $getUser->getRoleNames()[0];
        $data = array(
            'id'=>$getSurat->id,
            'id_user'=>$getSurat->id_user,
            'stb'=>$getSurat->stb,
            'name'=>$getSurat->nama_taruna,
            'grade'=>$getSurat->grade,
            'ttl'=>$getSurat->ttl,
            'orangtua'=>$getSurat->orangtua,
            'pekerjaan'=>$getSurat->pekerjaan,
            'alamat'=>$getSurat->alamat,
            'keperluan'=>$getSurat->keperluan,
            'created_at'=>date('Y-m-d', strtotime($getSurat->updated_at)),
            'created_at_bi'=>date('d-m-Y', strtotime($getSurat->updated_at)),
            'status'=>$getSurat->status,
            'photo'=>$getSurat->photo ? \URL::to('/')."/storage/".config('app.documentImagePath')."/suket/".$getSurat->photo : '',
            'form'=>['ttl', 'orangtua', 'pekerjaan', 'alamat', 'keperluan'],
            'status_disposisi'=> $getSurat->status_disposisi,
            'user_disposisi'=>$getSurat->user_disposisi,
            'date_disposisi'=>$getSurat->date_disposisi,
            'reason_disposisi'=>$getSurat->reason_disposisi,
            'user_approve_1'=>$getSurat->user_approve_1,
            'date_approve_1'=>$getSurat->date_approve_1,
            'status_level_1'=>$getSurat->status_level_1,
            'reason_level_1'=>$getSurat->user_reason_1,
            'user_approve_2'=>$getSurat->user_approve_2,
            'date_approve_2'=>$getSurat->date_approve_2,
            'status_level_2'=>$getSurat->status_level_2,
            'reason_level_2'=>$getSurat->user_reason_2,
            'show_disposisi'=>false,
            'show_persetujuan'=>false,
            'download'=>false
        );
        if(!empty($request->cetak)){
            return $data;
        }
        if($getSurat->status_disposisi==1){
            $status_disposisi = 'Disposisi';
        }else if ($getSurat->status_disposisi==0) {
            $status_disposisi = 'Belum Disposisi';
        }else {
            $status_disposisi = 'Disposisi Ditolak';
        }
        if($getSurat->status==1){
            $data['status_name'] = 'Disetujui';
        }else if ($getSurat->status==0) {
            $data['status_name'] = 'Belum Disetujui';
        }else {
            $data['status_name'] = 'Tidak Disetujui';
        }
    
        if($roleName=='Pembina' && $data['status_level_1']!=1 && $getSurat->status!=1){
            $data['show_disposisi'] = true;
        }

        $data['permission'] = [];
        if(($roleName=='Taruna' || $roleName=='Orang Tua') && $getSurat->status_disposisi!=1 && $getSurat->status!=1) {
            if($getSurat->user_created==$request->id_user){
                $data['permission'] = ['edit', 'delete'];
            }
        }
        if($roleName=='Akademik dan Ketarunaan' && $data['status_level_1']!=1 && $getSurat->status_disposisi==1){
            $data['show_persetujuan'] = true;
        }

        if($roleName=='Direktur' && $data['status']!=1 && $data['status_level_1']==1){
            $data['show_persetujuan'] = true;
        }

        if($getSurat['status']==1){
            $data['download'] = \URL::to('/').'/api/cetaksuket/id/'.$request->id.'/id_user/'.$request->id_user;
        }

        return $this->sendResponse($data, 'suket load successfully.');
    }
}
