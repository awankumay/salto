@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('post-category.edit', $postCategory) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Ubah Kategori Konten</div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($postCategory, ['method' => 'PATCH','route' => ['post-category.update', $postCategory->id], 'enctype' => 'multipart/form-data']) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Nama:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Nama Kategori','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Nama kategori wajib diisi *'}}
                        </span>
                        {!! Form::hidden('old_name', $postCategory->name, array('placeholder' => 'Nama Kategori','class' => 'form-control form-control-sm')) !!}

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Deskripsi:</strong>
                        {!! Form::textarea('description', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm', 'placeholder'=>'Deskripsi Kategori')) !!}
                        <span class="form-text {{isset($errors->messages()['description']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['description']) ? $errors->messages()['description'][0] .'*' : 'Deskripsi tidak wajid diisi'}}
                        </span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                            <a class="btn btn-sm btn-success" href="{{route('post-category.index')}}">Cancel</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
