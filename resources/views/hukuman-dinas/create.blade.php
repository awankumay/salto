@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('hukuman-dinas.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah Hukuman</div>
                <a class="btn btn-sm btn-warning" href="{{route('hukuman-dinas.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['hukuman-dinas.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Taruna:</strong>
                        {!! Form::select('id_taruna', [], [], array('class' => 'form-control form-control-sm taruna','single', 'placeholder'=>'Pilih Taruna')) !!}
                        <span class="form-text {{isset($errors->messages()['id_taruna']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['id_taruna']) ? $errors->messages()['id_taruna'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
            </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Tingkat:</strong>
                        {!! Form::select('tingkat', $tingkat, [], array('class' => 'form-control form-control-sm','single', 'placeholder'=>'Pilih Tingkat')) !!}
                        <span class="form-text {{isset($errors->messages()['tingkat']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['tingkat']) ? $errors->messages()['tingkat'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Hukuman:</strong>
                        @php isset($errors->messages()['hukuman']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('hukuman', null, array('placeholder' => 'hukuman','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['hukuman']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['hukuman']) ? $errors->messages()['hukuman'][0] .'*' : 'Hukuman wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Keterangan:</strong>
                        @php isset($errors->messages()['keterangan']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('keterangan', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'keterangan')) !!}
                        <span class="form-text {{isset($errors->messages()['keterangan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['keterangan']) ? $errors->messages()['keterangan'][0] .'*' : 'Keterangan wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Start Time:</strong>
                        @php isset($errors->messages()['start_time']) ? $x='is-invalid' : $x='' @endphp
                        <input type="datetime-local" class="form-control form-control-sm $x" name="start_time">
                        <span class="form-text {{isset($errors->messages()['start_time']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['start_time']) ? $errors->messages()['start_time'][0] .'*' : 'Start Time wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>End Time:</strong>
                        @php isset($errors->messages()['end_time']) ? $x='is-invalid' : $x='' @endphp
                        <input type="datetime-local" class="form-control form-control-sm $x" name="end_time">
                        <span class="form-text {{isset($errors->messages()['end_time']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['end_time']) ? $errors->messages()['end_time'][0] .'*' : 'End Time wajib diisi *'}}
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
