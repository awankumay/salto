@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('tags.edit', $tags) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Ubah Tags</div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($tags, ['method' => 'PATCH','route' => ['tags.update', $tags->id]]) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Nama:</strong>
                        @php isset($errors->messages()['name']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('name', null, array('placeholder' => 'Nama Kategori','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Nama kategori wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                        <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                        <a class="btn btn-sm btn-success" href="{{route('tags.index')}}">Kembali</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
