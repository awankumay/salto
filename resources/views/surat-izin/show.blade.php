@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('surat-izin.show', $getSurat) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Detail Surat Izin</div>
                <a class="btn btn-sm btn-warning" href="{{route('surat-izin.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($getSurat) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        @if($currentUser->getRoleNames()[0]!='Taruna')
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Nama Taruna:</strong>
                                <select class="taruna form-control form-control-sm" name="id_user">
                                @if($selectTaruna)<option value="{{$selectTaruna->id}}" selected>{{$selectTaruna->name}}</option>@endif
                                    <option value="">Pilih Taruna</option>
                                </select>
                                <span class="form-text {{isset($errors->messages()['id_user']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['id_user']) ? $errors->messages()['id_user'][0] .'*' : 'Pilih salah satu *'}}
                                </span>
                            </div>
                        </div>
                        <input type="hidden" name="id_user" value="{{$selectTaruna->id}}">
                        <input type="hidden" name="id_category" value="{{$getSurat->id_category}}">
                        @else
                        <input type="hidden" name="id_user" value="{{$selectTaruna->id}}">
                        @endif
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <strong>Kategori:</strong>
                                {!! Form::select('id_category', $suratIzin, $selectSuratIzin, array('class' => 'form-control form-control-sm kategori','single', 'placeholder'=>'Pilih kategori', 'readonly')) !!}
                                <span class="form-text {{isset($errors->messages()['id_category']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['id_category']) ? $errors->messages()['id_category'][0] .'*' : 'Pilih salah satu *'}}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Tanggal Mulai:</strong>
                                    @php isset($errors->messages()['start']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::date('start', $start, array('placeholder' => 'Pilih Tanggal','class' => 'form-control form-control-sm '.$x.'', 'required')) !!}
                                    <span class="form-text {{isset($errors->messages()['start']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['start']) ? $errors->messages()['start'][0] .'*' : 'Tanggal Mulai wajib diisi * '}}
                                    </span>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Waktu Mulai:</strong>
                                    @php isset($errors->messages()['start_time']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::time('start_time', $start_time, array('placeholder' => 'Pilih Waktu','class' => 'form-control form-control-sm '.$x.'', 'required')) !!}
                                    <span class="form-text {{isset($errors->messages()['start_time']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['start_time']) ? $errors->messages()['start_time'][0] .'*' : 'Waktu Mulai wajib diisi * '}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Tanggal Akhir:</strong>
                                    @php isset($errors->messages()['end']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::date('end', $end, array('placeholder' => 'Pilih Tanggal','class' => 'form-control form-control-sm '.$x.'', 'required')) !!}
                                    <span class="form-text {{isset($errors->messages()['end']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['end']) ? $errors->messages()['end'][0] .'*' : 'Tanggal Akhir wajib diisi * '}}
                                    </span>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Waktu Akhir:</strong>
                                    @php isset($errors->messages()['end_time']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::time('end_time', $end_time, array('placeholder' => 'Pilih Waktu','class' => 'form-control form-control-sm '.$x.'', 'required')) !!}
                                    <span class="form-text {{isset($errors->messages()['end_time']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['end_time']) ? $errors->messages()['end_time'][0] .'*' : 'Waktu Akhir wajib diisi * '}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                    <div id="umum" class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Keperluan:</strong>
                                    @php isset($errors->messages()['keperluan']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::textarea('keperluan', isset($getSuratDetail->keperluan) ? $getSuratDetail->keperluan : '' , array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Keperluan')) !!}
                                    <span class="form-text {{isset($errors->messages()['keperluan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['keperluan']) ? $errors->messages()['keperluan'][0] .'*' : 'Keperluan wajib diisi *'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Tujuan:</strong>
                                    @php isset($errors->messages()['tujuan']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::textarea('tujuan', isset($getSuratDetail->tujuan) ? $getSuratDetail->tujuan : '', array('rows' => 3, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Tujuan')) !!}
                                    <span class="form-text {{isset($errors->messages()['tujuan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['tujuan']) ? $errors->messages()['tujuan'][0] .'*' : 'Tujuan wajib diisi *'}}
                                    </span>
                                </div>
                            </div> 
                        </div>
                        <div id="keluar-kampus" class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Keperluan:</strong>
                                    @php isset($errors->messages()['keperluan']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::textarea('keperluan', isset($getSuratDetail->keperluan) ? $getSuratDetail->keperluan : '', array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Keperluan')) !!}
                                    <span class="form-text {{isset($errors->messages()['keperluan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['keperluan']) ? $errors->messages()['keperluan'][0] .'*' : 'Keperluan wajib diisi *'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Pendamping:</strong>
                                    @php isset($errors->messages()['pendamping']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::text('pendamping', isset($getSuratDetail->pendamping) ? $getSuratDetail->pendamping : '', array('placeholder' => 'Nama Pendamping','class' => 'form-control form-control-sm '.$x.'')) !!}
                                    <span class="form-text {{isset($errors->messages()['pendamping']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['pendamping']) ? $errors->messages()['pendamping'][0] .'*' : 'Nama Pendamping wajib diisi *'}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div id="training" class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Training:</strong>
                                    @php isset($errors->messages()['nm_tc']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::text('nm_tc', isset($getSuratDetail->nm_tc) ? $getSuratDetail->nm_tc : '', array('placeholder' => 'Nama Training','class' => 'form-control form-control-sm '.$x.'')) !!}
                                    <span class="form-text {{isset($errors->messages()['nm_tc']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['nm_tc']) ? $errors->messages()['nm_tc'][0] .'*' : 'Nama Training wajib diisi * '}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Pelatih:</strong>
                                    @php isset($errors->messages()['pelatih']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::text('pelatih', isset($getSuratDetail->pelatih) ? $getSuratDetail->pelatih : '', array('placeholder' => 'Nama Pelatih','class' => 'form-control form-control-sm '.$x.'')) !!}
                                    <span class="form-text {{isset($errors->messages()['pelatih']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['pelatih']) ? $errors->messages()['pelatih'][0] .'*' : 'Nama Pelatih wajib diisi *'}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div id="izin-sakit" class="row col-md-12">
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Keluhan:</strong>
                                    @php isset($errors->messages()['keluhan']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::textarea('keluhan', isset($getSuratDetail->keluhan) ? $getSuratDetail->keluhan : '', array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Keluhan')) !!}
                                    <span class="form-text {{isset($errors->messages()['keluhan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['keluhan']) ? $errors->messages()['keluhan'][0] .'*' : 'Keluhan wajib diisi'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Diagnosa:</strong>
                                    @php isset($errors->messages()['diagnosa']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::textarea('diagnosa', isset($getSuratDetail->diagnosa) ? $getSuratDetail->diagnosa : '', array('rows' => 3, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Diagnosa')) !!}
                                    <span class="form-text {{isset($errors->messages()['diagnosa']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['diagnosa']) ? $errors->messages()['diagnosa'][0] .'*' : 'Diagnosa tidak wajib diisi'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Rekomendasi:</strong>
                                    @php isset($errors->messages()['rekomendasi']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::textarea('rekomendasi', isset($getSuratDetail->rekomendasi) ? $getSuratDetail->rekomendasi : '', array('rows' => 2, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Rekomendasi')) !!}
                                    <span class="form-text {{isset($errors->messages()['rekomendasi']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['rekomendasi']) ? $errors->messages()['rekomendasi'][0] .'*' : 'Rekomendasi tidak wajib diisi'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Dokter:</strong>
                                    @php isset($errors->messages()['dokter']) ? $x='is-invalid' : $x='' @endphp
                                    {!! Form::text('dokter', isset($getSuratDetail->dokter) ? $getSuratDetail->dokter : '', array('placeholder' => 'Nama Dokter','class' => 'form-control form-control-sm '.$x.'')) !!}
                                    <span class="form-text {{isset($errors->messages()['dokter']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['dokter']) ? $errors->messages()['dokter'][0] .'*' : 'Nama dokter tidak wajib diisi'}}
                                    </span>
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
                                @if($getSurat->photo)<img src="{{URL::to('/')}}/storage/{{config('app.documentImagePath')}}/{{$getSurat->photo}}" width="100%"/>@endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                <button style="font-size:12px;margin:5px;" type="button" class="btn btn-sm btn-success">
                                    Disetujui
                                </button>
                                <button style="font-size:12px;margin:5px;" type="button" class="btn btn-sm btn-danger">
                                    Tidak Disetujui
                                </button>
                                <button style="font-size:12px;margin:5px;" type="button" class="btn btn-sm btn-info">
                                    Belum Disetujui
                                </button>
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
    function deleteExist(fileName, id) {
        let deleteUrl = 'deleteExistImageSurat';
        let token ="{{csrf_token()}}";
        let params = {
           'image':fileName, 'id':id, "_token": token,
        }
        swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true
            }).then((willDelete) => {
                    if (willDelete) {
                        $(document).ajaxSend(function() {
                            $("#overlay").fadeIn(300);　
                        });
                        $.ajax({
                            url: "{{url('/')}}/dashboard/"+deleteUrl,
                            type: 'POST',
                            data: params,
                            success:function(){
                                window.location.reload(true);
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Error, image deleted failure");
			                    },500);
                            }
                        });
                    } else {
                        //swal("Your imaginary file is safe!");
                    }
            });
    }
$(function () {
    //$('.kategori').select2();
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
   /*  $('.taruna').select2({
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Masukan Nama Taruna',
        ajax: {
            dataType: 'json',
            url: '/dashboard/gettaruna',
            delay: 300,
            data: function(params) {
                return {
                    search: params.term
                }
            },
            processResults: function (data, page) {
                return {
                    results: data
                };
            },
        }
    }).on('taruna:select', function (evt) {
         var data = $(".taruna option:selected").text();
    }); */

});
</script>
@endpush
@endsection
