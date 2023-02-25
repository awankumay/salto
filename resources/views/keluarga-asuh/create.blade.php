@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('keluarga-asuh.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah Keluarga Asuh</div>
                <a class="btn btn-sm btn-warning" href="{{route('keluarga-asuh.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['keluarga-asuh.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Nama:</strong>
                        @php isset($errors->messages()['name']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('name', null, array('placeholder' => 'Nama Keluarga Asuh','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Nama keluarga asuh wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Deskripsi:</strong>
                        @php isset($errors->messages()['description']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('description', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Deskripsi')) !!}
                        <span class="form-text {{isset($errors->messages()['description']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['description']) ? $errors->messages()['description'][0] .'*' : 'Deskripsi tidak wajib diisi'}}
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
