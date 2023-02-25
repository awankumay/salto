@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('suket.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah Surat Keterangan</div>
                <a class="btn btn-sm btn-warning" href="{{route('suket.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['suket.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                @if($currentUser->getRoleNames()[0]=='Super Admin')
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Nama Taruna:</strong>
                            {!! Form::select('id_user', [], [], array('class' => 'form-control form-control-sm taruna','single', 'placeholder'=>'Pilih Taruna')) !!}
                            <span class="form-text {{isset($errors->messages()['id_user']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['id_user']) ? $errors->messages()['id_user'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="id_user" value="{{$currentUser->id}}">
                @endif
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Tempat & Tanggal Lahir:</strong>
                        @php isset($errors->messages()['ttl']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('ttl', null, array('placeholder' => 'Tempat & Tanggal Lahir','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['ttl']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['ttl']) ? $errors->messages()['ttl'][0] .'*' : 'Tempat tanggal lahir wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Pekerjaan:</strong>
                        @php isset($errors->messages()['pekerjaan']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('pekerjaan', null, array('placeholder' => 'Pekerjaan','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['pekerjaan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['pekerjaan']) ? $errors->messages()['pekerjaan'][0] .'*' : 'Pekerjaan *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Orang Tua:</strong>
                        @php isset($errors->messages()['orangtua']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('orangtua', null, array('placeholder' => 'orangtua','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['orangtua']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['orangtua']) ? $errors->messages()['orangtua'][0] .'*' : 'Orang Tua *'}}
                        </span>
                    </div>
                </div>
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
                        <strong>Alamat:</strong>
                        @php isset($errors->messages()['alamat']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('alamat', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'alamat')) !!}
                        <span class="form-text {{isset($errors->messages()['alamat']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['alamat']) ? $errors->messages()['alamat'][0] .'*' : 'alamat wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6" ng-controller="SelectFileController">
                        <strong>Lampiran:</strong><br>
                        <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                        <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:400px" /></div>
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Lampiran tidak wajib diisi'}}
                        </span>
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
    $('.taruna').select2({
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
    });
});
</script>
@endpush
@endsection
