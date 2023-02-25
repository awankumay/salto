@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('materi-wbs.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah Materi WBS </div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['materi-wbs.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Nama Materi WBS:</strong>
                        @php isset($errors->messages()['nama_materi']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('nama_materi', null, array('placeholder' => 'Materi WBS','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['nama_materi']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['nama_materi']) ? $errors->messages()['nama_materi'][0] .'*' : 'Materi WBS wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <button type="submit" class="btn btn-sm btn-danger">Simpan</button>
                            <a class="btn btn-sm btn-warning" href="{{route('materi-wbs.index')}}">Kembali</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
