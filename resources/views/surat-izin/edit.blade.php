@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('surat-izin.edit', $getSurat) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Ubah Surat Izin</div>
                <a class="btn btn-sm btn-warning" href="{{route('surat-izin.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($getSurat, ['method' => 'PATCH','route' => ['surat-izin.update', $getSurat->id], 'enctype' => 'multipart/form-data']) !!}
            <div class="row">
                @if($currentUser->getRoleNames()[0]!='Taruna')
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Nama Taruna:</strong>
                            {!! Form::select('id_user', [], $getSurat->id_user, array('class' => 'form-control form-control-sm taruna','single', 'placeholder'=>'Pilih Taruna')) !!}
                            <span class="form-text {{isset($errors->messages()['id_user']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['id_user']) ? $errors->messages()['id_user'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="id_user" value="{{$currentUser->id}}">
                @endif
                <div class="col-md-12">
                    <div class="row col-md-6">
                        <div class="form-group col-md-6">
                            <strong>Tanggal Mulai:</strong>
                            @php isset($errors->messages()['start']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::date('start', null, array('placeholder' => 'Pilih Tanggal','class' => 'form-control form-control-sm '.$x.'', 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['start']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['start']) ? $errors->messages()['start'][0] .'*' : 'Tanggal Mulai wajib diisi * '}}
                            </span>
                        </div>
                        <div class="form-group col-md-6">
                            <strong>Waktu Mulai:</strong>
                            @php isset($errors->messages()['start_time']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::time('start_time', null, array('placeholder' => 'Pilih Waktu','class' => 'form-control form-control-sm '.$x.'', 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['start_time']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['start_time']) ? $errors->messages()['start_time'][0] .'*' : 'Waktu Mulai wajib diisi * '}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row col-md-6">
                        <div class="form-group col-md-6">
                            <strong>Tanggal Akhir:</strong>
                            @php isset($errors->messages()['end']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::date('end', null, array('placeholder' => 'Pilih Tanggal','class' => 'form-control form-control-sm '.$x.'', 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['end']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['end']) ? $errors->messages()['end'][0] .'*' : 'Tanggal Akhir wajib diisi * '}}
                            </span>
                        </div>
                        <div class="form-group col-md-6">
                            <strong>Waktu Akhir:</strong>
                            @php isset($errors->messages()['end_time']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::time('end_time', null, array('placeholder' => 'Pilih Waktu','class' => 'form-control form-control-sm '.$x.'', 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['end_time']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['end_time']) ? $errors->messages()['end_time'][0] .'*' : 'Waktu Akhir wajib diisi * '}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Kategori:</strong>
                        {!! Form::select('id_category', $suratIzin, $selectSuratIzin, array('class' => 'form-control form-control-sm kategori','single', 'placeholder'=>'Pilih kategori')) !!}
                        <span class="form-text {{isset($errors->messages()['id_category']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['id_category']) ? $errors->messages()['id_category'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6" ng-controller="SelectFileController">
                        <strong>Lampiran:</strong><br>
                        <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                        <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:400px" /></div>
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Ukuran foto < 300kb *'}}
                        </span>
                    </div>
                </div>
                <div id="umum" class="row col-md-12">
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Keperluan:</strong>
                            @php isset($errors->messages()['keperluan']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::textarea('keperluan', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Keperluan')) !!}
                            <span class="form-text {{isset($errors->messages()['keperluan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['keperluan']) ? $errors->messages()['keperluan'][0] .'*' : 'Keperluan wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Tujuan:</strong>
                            @php isset($errors->messages()['tujuan']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::textarea('tujuan', null, array('rows' => 3, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Tujuan')) !!}
                            <span class="form-text {{isset($errors->messages()['tujuan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['tujuan']) ? $errors->messages()['tujuan'][0] .'*' : 'Tujuan wajib diisi *'}}
                            </span>
                        </div>
                    </div> 
                </div>
                <div id="keluar-kampus" class="row col-md-12">
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Keperluan:</strong>
                            @php isset($errors->messages()['keperluan']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::textarea('keperluan', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Keperluan')) !!}
                            <span class="form-text {{isset($errors->messages()['keperluan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['keperluan']) ? $errors->messages()['keperluan'][0] .'*' : 'Keperluan wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Pendamping:</strong>
                            @php isset($errors->messages()['pendamping']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('pendamping', null, array('placeholder' => 'Nama Pendamping','class' => 'form-control form-control-sm '.$x.'')) !!}
                            <span class="form-text {{isset($errors->messages()['pendamping']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['pendamping']) ? $errors->messages()['pendamping'][0] .'*' : 'Nama Pendamping wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </div>
                <div id="training" class="row col-md-12">
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Training:</strong>
                            @php isset($errors->messages()['nm_tc']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('nm_tc', null, array('placeholder' => 'Nama Training','class' => 'form-control form-control-sm '.$x.'')) !!}
                            <span class="form-text {{isset($errors->messages()['nm_tc']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['nm_tc']) ? $errors->messages()['nm_tc'][0] .'*' : 'Nama Training wajib diisi * '}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Pelatih:</strong>
                            @php isset($errors->messages()['pelatih']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('pelatih', null, array('placeholder' => 'Nama Pelatih','class' => 'form-control form-control-sm '.$x.'')) !!}
                            <span class="form-text {{isset($errors->messages()['pelatih']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['pelatih']) ? $errors->messages()['pelatih'][0] .'*' : 'Nama Pelatih wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </div>
                <div id="izin-sakit" class="row col-md-12">
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Keluhan:</strong>
                            @php isset($errors->messages()['keluhan']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::textarea('keluhan', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Keluhan')) !!}
                            <span class="form-text {{isset($errors->messages()['keluhan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['keluhan']) ? $errors->messages()['keluhan'][0] .'*' : 'Keluhan wajib diisi'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Diagnosa:</strong>
                            @php isset($errors->messages()['diagnosa']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::textarea('diagnosa', null, array('rows' => 3, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Diagnosa')) !!}
                            <span class="form-text {{isset($errors->messages()['diagnosa']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['diagnosa']) ? $errors->messages()['diagnosa'][0] .'*' : 'Diagnosa tidak wajib diisi'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Rekomendasi:</strong>
                            @php isset($errors->messages()['rekomendasi']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::textarea('rekomendasi', null, array('rows' => 2, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Rekomendasi')) !!}
                            <span class="form-text {{isset($errors->messages()['rekomendasi']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['rekomendasi']) ? $errors->messages()['rekomendasi'][0] .'*' : 'Rekomendasi tidak wajib diisi'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Dokter:</strong>
                            @php isset($errors->messages()['dokter']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('dokter', null, array('placeholder' => 'Nama Dokter','class' => 'form-control form-control-sm '.$x.'')) !!}
                            <span class="form-text {{isset($errors->messages()['dokter']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['dokter']) ? $errors->messages()['dokter'][0] .'*' : 'Nama dokter tidak wajib diisi'}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <button type="submit" class="btn btn-sm btn-danger">Simpan</button>
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
    $('.kategori').select2();
    $(window).on('load', function () {
        var kategori = $('#kategori').val();
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
    }
    $('.taruna').select2({
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Masukkan Nama Taruna',
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
    });
});
</script>
@endpush
@endsection
