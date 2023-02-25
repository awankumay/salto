@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('hukuman-dinas.show', $data) }}
    </div>

    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Detail Hukuman  - {{$data->nama_taruna}} / {{$data->stb}}</div>
                <a class="btn btn-sm btn-warning" href="{{route('hukuman-dinas.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($data) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Taruna:</strong>
                                <div class="form-text">{{$data->nama_taruna}}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Pembina</strong>
                                    <div class="form-text">{{$data->nama_pembina}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Hukuman</strong>
                                    <div class="form-text">{{$data->hukuman}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Tingkat Hukuman:</strong>
                                <div class="form-text">{{!empty($data->tingkat_name) ? $data->tingkat_name : ''}}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Keterangan Hukuman:</strong>
                                <div class="form-text">{{!empty($data->keterangan) ? $data->keterangan : ''}}</div>
                            </div>
                        </div> 
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Waktu:</strong>
                                <div class="form-text">{{!empty($data->start_time_bi) ? $data->start_time_bi : ''}} s/d {{!empty($data->start_time_bi) ? $data->end_time_bi : ''}}</div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Aak : </strong>
                                <div class="form-text mb-3">{{$data->reason_level_1}}</div>
                                
                                
                                <strong>Status : </strong>
                                <div class="form-text">
                                @if($data->status==1)
                                Disetujui
                                @elseif($data->status==2)
                                Tidak Disetujui
                                @else
                                Belum Disetujui
                                @endif
                                </div>
                                @if($data->status==1 && (auth()->user()->getRoleNames()['0']=='Orang Tua' || auth()->user()->getRoleNames()['0']=='Taruna'))
                                    <button type="button" class="btn btn-sm btn-danger" onclick="download()">Download</button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                            @if($data->show_persetujuan==true)
                                <button data-toggle="modal" data-target="#persetujuan" style="font-size:12px;" type="button" class="btn btn-sm btn-danger">
                                    Persetujuan Surat Keterangan
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
                                                    <input type="hidden" name="id" id="id" value="{{$data->id}}">
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
    });

    function persetujuanSurat(status) {	
        fd = new FormData();
        fd.append('id', "{{$data->id}}");	      
        fd.append('id_user', "{{Auth::user()->id}}");	      
        fd.append('reason', $('#reason').val());	      
        fd.append('status', status);
        $(".modal").css( "z-index", 1);  		      
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{url('/')}}/dashboard/approvehukdis", 
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
            url: "{{url('/')}}/api/cetaksurat/id/{{$data->id}}/id_user/{{$data->id_user}}/cetak/hukdis", 
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
