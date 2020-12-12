@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('surat-izin.show', $getSurat) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Detail Surat Izin - {{$selectTaruna->name}} / {{$selectTaruna->stb}}</div>
                <a class="btn btn-sm btn-warning" href="{{route('surat-izin.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($getSurat) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Kategori:</strong>
                                <div class="form-text">{{$selectSuratIzin->nama_menu}}</div>
                                <input type="hidden" name="kategori" class="kategori" value="{{$getSurat->id_category}}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Tanggal Mulai:</strong>
                                    <div class="form-text">{{$start}} {{$start_time}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Tanggal Akhir:</strong>
                                    <div class="form-text">{{$end}} {{$end_time}}</div>
                                </div>
                            </div>
                        </div>
                        <div id="umum" class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Keperluan:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->keperluan) ? $getSuratDetail->keperluan : ''}}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Tujuan:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->tujuan) ? $getSuratDetail->tujuan : ''}}</div>
                                </div>
                            </div> 
                        </div>
                        <div id="keluar-kampus" class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Keperluan:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->keperluan) ? $getSuratDetail->keperluan : ''}}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Pendamping:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->pendamping) ? $getSuratDetail->pendamping : ''}}</div>
                                </div>
                            </div>
                        </div>
                        <div id="training" class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Training:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->nm_tc) ? $getSuratDetail->nm_tc : ''}}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Pelatih:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->pelatih) ? $getSuratDetail->pelatih : ''}}</div>
                                </div>
                            </div>
                        </div>
                        <div id="izin-sakit" class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Keluhan:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->keluhan) ? $getSuratDetail->keluhan : ''}}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Diagnosa:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->diagnosa) ? $getSuratDetail->diagnosa : ''}}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Rekomendasi:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->rekomendasi) ? $getSuratDetail->rekomendasi : ''}}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Dokter:</strong>
                                    <div class="form-text">{{!empty($getSuratDetail->dokter) ? $getSuratDetail->dokter : ''}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-12" ng-controller="SelectFileController">
                                <strong>Lampiran:</strong><br>
                                @if($getSurat->photo)
                                <img src="{{URL::to('/')}}/storage/{{config('app.documentImagePath')}}/{{$getSurat->photo}}" width="100%"/> 
                                @else
                                Tidak ada Lampiran
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Pembina : </strong>
                                <div class="form-text mb-3">{{$getSurat->reason_disposisi}}</div>
                                <strong>Aak : </strong>
                                <div class="form-text mb-3">{{$getSurat->reason_level_1}}</div>
                                <strong>Direktur : </strong>
                                <div class="form-text mb-3">{{$getSurat->reason_level_2}}</div>
                                
                                <strong>Status : </strong>
                                <div class="form-text">
                                @if($getSurat->status==1)
                                Disetujui
                                @elseif($getSurat->status==2)
                                Tidak Disetujui
                                @else
                                Belum Disetujui
                                @endif
                                </div>
                                @if($getSurat->status==1 && (auth()->user()->getRoleNames()['0']=='Orang Tua' || auth()->user()->getRoleNames()['0']=='Taruna'))
                                    <button type="button" class="btn btn-sm btn-danger" onclick="download()">Download</button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                            @if(auth()->user()->getRoleNames()['0']=='Pembina' && $getSurat->status_level_1!=1 && $getSurat->status!=1)
                                <button data-toggle="modal" data-target="#disposisi" style="font-size:12px;" type="button" class="btn btn-sm btn-danger">
                                    Disposisi
                                </button> 
                                <div class="modal fade" id="disposisi" tabindex="-1" role="dialog" aria-labelledby="disposisiLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="disposisiLabel">Diposisi</h5>
                                                <button type="button" class="close" onclick="refresh();" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form name="disposisi_surat" id="disposisi_surat">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_user" id="id_user" value="{{Auth::user()->id}}">
                                                    <input type="hidden" name="id" id="id" value="{{$getSurat->id}}">
                                                    <textarea name="reason" id="reason" class="form-control form-control-sm"></textarea>
                                                    <span class="form-text">
                                                        <span class="text-danger text-help" id="reason-error"></span>
                                                    </span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" onclick="disposisiSurat(0);">Tidak</button>
                                                    <button type="button" class="btn btn-danger" onclick="disposisiSurat(1);">Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if((auth()->user()->getRoleNames()['0']=='Akademik dan Ketarunaan' || auth()->user()->getRoleNames()['0']=='Super Admin') && $getSurat->status!=1 && $getSurat->status_level_1!=1 && $getSurat->status_disposisi==1)
                            <button data-toggle="modal" data-target="#persetujuan" style="font-size:12px;" type="button" class="btn btn-sm btn-danger">
                                Persetujuan Surat - Aak
                            </button> 
                            <div class="modal fade" id="persetujuan" tabindex="-1" role="dialog" aria-labelledby="persetujuanLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="persetujuanLabel">Persetujuan</h5>
                                            <button type="button" class="close" onclick="refresh();" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form name="persetujuan_surat" id="persetujuan_surat">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_user" id="id_user" value="{{Auth::user()->id}}">
                                                <input type="hidden" name="id" id="id" value="{{$getSurat->id}}">
                                                <textarea name="reason" id="reason" class="form-control form-control-sm"></textarea>
                                                <span class="form-text">
                                                    <span class="text-danger text-help" id="reason-error"></span>
                                                </span>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" onclick="persetujuanSurat(0);">Tidak</button>
                                                <button type="button" class="btn btn-danger" onclick="persetujuanSurat(1);">Ya</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(strtotime(date_format(date_create($getSurat->end), 'Y-m-d')) > strtotime(date_format(date_create($getSurat->start), 'Y-m-d')))    
                                @if((auth()->user()->getRoleNames()['0']=='Direktur' || auth()->user()->getRoleNames()['0']=='Super Admin') && $getSurat->status!=1 && $getSurat->status_level_1==1)
                                <button data-toggle="modal" data-target="#persetujuandirut" style="font-size:12px;" type="button" class="btn btn-sm btn-danger">
                                    Persetujuan Surat - Direktur
                                </button> 
                                <div class="modal fade" id="persetujuandirut" tabindex="-1" role="dialog" aria-labelledby="persetujuandirutLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="persetujuandirutLabel">Persetujuan</h5>
                                                <button type="button" class="close" onclick="refresh();" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form name="persetujuan_surat" id="persetujuan_surat">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_user" id="id_user" value="{{Auth::user()->id}}">
                                                    <input type="hidden" name="id" id="id" value="{{$getSurat->id}}">
                                                    <textarea name="reason" id="reason" class="form-control form-control-sm"></textarea>
                                                    <span class="form-text">
                                                        <span class="text-danger text-help" id="reason-error"></span>
                                                    </span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" onclick="persetujuanSurat(0);">Tidak</button>
                                                    <button type="button" class="btn btn-danger" onclick="persetujuanSurat(1);">Ya</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif     
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    $(function () {
        $(window).on('load', function () {
            var kategori = $('.kategori').val();
            switchForm(kategori);
        });
        $('.kategori').change(function () {
        var kategori = $('.kategori').val();
        switchForm(kategori);
        });
        function switchForm(kategori) {
            if(kategori==1){
                $('#izin-sakit').css('display', 'block');
                $("#izin-sakit :input").prop("disabled", false);
                $('#keluar-kampus').css('display', 'none');
                $("#keluar-kampus :input").prop("disabled", true);
                $('#training').css('display', 'none');
                $("#training :input").prop("disabled", true);
                $('#umum').css('display', 'none');
                $("#umum :input").prop("disabled", true);
            }else if(kategori==2){
                $('#izin-sakit').css('display', 'none');
                $("#izin-sakit :input").prop("disabled", true);
                $('#keluar-kampus').css('display', 'block');
                $("#keluar-kampus :input").prop("disabled", false);
                $('#training').css('display', 'none');
                $("#training :input").prop("disabled", true);
                $('#umum').css('display', 'none');
                $("#umum :input").prop("disabled", true);
            }else if(kategori==3){
                $('#izin-sakit').css('display', 'none');
                $("#izin-sakit :input").prop("disabled", true);
                $('#keluar-kampus').css('display', 'none');
                $("#keluar-kampus :input").prop("disabled", true);
                $('#training').css('display', 'block');
                $("#training :input").prop("disabled", false);
                $('#umum').css('display', 'none');
                $("#umum :input").prop("disabled", true);
            }else{
                $('#izin-sakit').css('display', 'none');
                $("#izin-sakit :input").prop("disabled", true);
                $('#keluar-kampus').css('display', 'none');
                $("#keluar-kampus :input").prop("disabled", true);
                $('#training').css('display', 'none');
                $("#training :input").prop("disabled", true);
                $('#umum').css('display', 'block');
                $("#umum :input").prop("disabled", false);
            }
            $('.kategori').attr('disabled', true);
            $('.taruna').attr('disabled', true);
        }
    });

    function disposisiSurat(status) {	
        fd = new FormData();
        fd.append('id', "{{$getSurat->id}}");	      
        fd.append('id_user', "{{Auth::user()->id}}");	      
        fd.append('reason', $('#reason').val());	      
        fd.append('status', status);	      
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{url('/')}}/dashboard/disposisisuratizin", 
            data: fd,
            processData: false,
            contentType: false,
            beforeSend:function () {
                $("#overlay").fadeIn(300);
            },
            type: 'POST',
            success: function(data, textStatus, xhr){
                $("#overlay").fadeOut();
                toastr.success("Sukses, Disposisi Berhasil");
                setTimeout(() => {
                    window.location.reload();
                }, 300);
            },
            error : function(data, textStatus, xhr) {
                $("#overlay").fadeOut();
                if(data.responseJSON.error){
                    if(data.responseJSON.error.reason){
                        $('#reason-error').text(data.responseJSON.error.reason[0])
                    }
                }
            }
        });
    }

    function persetujuanSurat(status) {	
        fd = new FormData();
        fd.append('id', "{{$getSurat->id}}");	      
        fd.append('id_user', "{{Auth::user()->id}}");	      
        fd.append('reason', $('#reason').val());	      
        fd.append('status', status);	      
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{url('/')}}/dashboard/approvesuratizin", 
            data: fd,
            processData: false,
            contentType: false,
            beforeSend:function () {
                $("#overlay").fadeIn(300);
            },
            type: 'POST',
            success: function(data, textStatus, xhr){
                $("#overlay").fadeOut();
                toastr.success("Sukses, Persetujuan Berhasil");
                setTimeout(() => {
                    window.location.reload();
                }, 300);
            },
            error : function(data, textStatus, xhr) {
                $("#overlay").fadeOut();
                if(data.responseJSON.error){
                    if(data.responseJSON.error.reason){
                        $('#reason-error').text(data.responseJSON.error.reason[0])
                    }
                }
            }
        });
    }

    function refresh() {
        window.location.reload()
    }
    function download() {
        $.ajaxSetup({
        });
        $.ajax({
            url: "{{url('/')}}/api/cetaksurat/id/{{$getSurat->id}}/id_user/{{$getSurat->id_user}}/cetak/perizinan", 
            processData: false,
            contentType: false,
            beforeSend:function () {
                $("#overlay").fadeIn(300);
            },
            type: 'GET',
            success: function(data, textStatus, xhr){
                $("#overlay").fadeOut();
                toastr.success("Sukses", data.message);
                if(data.success==true){
                    window.open(data.data.link, '_blank')
                }
            },
            error : function(data, textStatus, xhr) {
                $("#overlay").fadeOut();
                if(data.responseJSON.error){
                    if(data.responseJSON.error.reason){
                        $('#reason-error').text(data.responseJSON.error.reason[0])
                    }
                }
            }
        });
        /* $data['download'] = \URL::to('/').'/api/cetaksurat/id/'.$request->id.'/id_user/'.$request->id_user.'/cetak/perizinan'; */
    }
</script>
@endpush
@endsection
