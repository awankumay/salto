@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('suket.show', $data) }}
    </div>

    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Detail Surat Keterangan - {{$data->name}} / {{$data->stb}}</div>
                <a class="btn btn-sm btn-warning" href="{{route('suket.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($data) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Nama:</strong>
                                <div class="form-text">{{$data->name}}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Tempat & Tanggal Lahir</strong>
                                    <div class="form-text">{{$data->ttl}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Nama Orang Tua</strong>
                                    <div class="form-text">{{$data->orangtua}}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Pekerjaan:</strong>
                                <div class="form-text">{{!empty($data->pekerjaan) ? $data->pekerjaan : ''}}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Keperluan:</strong>
                                <div class="form-text">{{!empty($data->keperluan) ? $data->keperluan : ''}}</div>
                            </div>
                        </div> 
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Alamat:</strong>
                                <div class="form-text">{{!empty($data->alamat) ? $data->alamat : ''}}</div>
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-12" ng-controller="SelectFileController">
                                <strong>Lampiran:</strong><br>
                                @if($data->photo)
                                <img src="{{$data->photo}}" width="100%"/> 
                                @else
                                Tidak ada Lampiran
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Pembina : </strong>
                                <div class="form-text mb-3">{{$data->reason_disposisi}}</div>
                                <strong>Aak : </strong>
                                <div class="form-text mb-3">{{$data->reason_level_1}}</div>
                                <strong>Direktur : </strong>
                                <div class="form-text mb-3">{{$data->reason_level_2}}</div>
                                
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
                            @if(auth()->user()->getRoleNames()['0']=='Pembina' && $data->status_level_1!=1 && $data->status!=1)
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
                                                    <input type="hidden" name="id" id="id" value="{{$data->id}}">
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

    function disposisiSurat(status) {	
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
            url: "{{url('/')}}/dashboard/disposisisuket", 
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
            url: "{{url('/')}}/dashboard/approvesuket", 
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
            url: "{{url('/')}}/api/cetaksurat/id/{{$data->id}}/id_user/{{$data->id_user}}/cetak/suket", 
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
