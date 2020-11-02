@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('surat-izin.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah Surat Izin</div>
                <a class="btn btn-sm btn-warning" href="{{route('surat-izin.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['surat-izin.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-12">
                        <strong>Nama Taruna:</strong>
                        {!! Form::select('id_taruna', [], [], array('class' => 'form-control form-control-sm taruna','single', 'placeholder'=>'Pilih Taruna')) !!}
                        <span class="form-text {{isset($errors->messages()['id_taruna']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['id_taruna']) ? $errors->messages()['id_taruna'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-12">
                        <strong>Kategori:</strong>
                        {!! Form::select('id_category', [], [], array('class' => 'form-control form-control-sm kategori','single', 'placeholder'=>'Pilih kategori')) !!}
                        <span class="form-text {{isset($errors->messages()['id_category']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['id_category']) ? $errors->messages()['id_category'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Keluhan:</strong>
                        @php isset($errors->messages()['keluhan']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('keluhan', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Keluhan', 'required')) !!}
                        <span class="form-text {{isset($errors->messages()['keluhan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['keluhan']) ? $errors->messages()['keluhan'][0] .'*' : 'Keluhan wajib diisi'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Diagnosa:</strong>
                        @php isset($errors->messages()['diagnosa']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('diagnosa', null, array('rows' => 3, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Diagnosa', 'required')) !!}
                        <span class="form-text {{isset($errors->messages()['diagnosa']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['diagnosa']) ? $errors->messages()['diagnosa'][0] .'*' : 'Diagnosa wajib diisi'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Rekomendasi:</strong>
                        @php isset($errors->messages()['rekomendasi']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('rekomendasi', null, array('rows' => 2, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Rekomendasi', 'required')) !!}
                        <span class="form-text {{isset($errors->messages()['rekomendasi']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['rekomendasi']) ? $errors->messages()['rekomendasi'][0] .'*' : 'Rekomendasi wajib diisi'}}
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
@endsection
