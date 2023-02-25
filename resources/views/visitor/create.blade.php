@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('product.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah product baru</div>
                <div class="p-2">
                    <a class="btn btn-sm btn-success float-right" href="{{route('product.index')}}">Kembali</a></div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['product.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                <div class="col-lg-8 col-sm-12 col-md-12">
                    <div class="form-group col-md-12">
                        <strong>Nama  </strong>{{-- <i class="text-help text-danger">(sisa karakter <%= 100-title.length %>)</i> --}}
                        {!! Form::text('name', null, array('placeholder' => 'Nama produk', 'ng-trim'=>'false', 'maxlength'=>'100', /* 'ng-model'=>'title', */ 'id'=>'title', 'class' => 'form-control form-control-sm editable', 'maxlength'=>'100')) !!}
                        <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Nama produk wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12" ng-controller="SelectFileController">
                        <strong>Foto</strong>
                        <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                        <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:200px" /></div>
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Ukuran foto < 300kb *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Tipe:</strong>
                        {!! Form::select('type', $typeCategory, [], array('class' => 'form-control form-control-sm','single', 'placeholder'=>'Pilih tipe produk')) !!}
                        <span class="form-text {{isset($errors->messages()['type']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['type']) ? $errors->messages()['type'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Kategori:</strong>
                        {!! Form::select('id_categories', $productCategory, [], array('class' => 'form-control form-control-sm','single', 'placeholder'=>'Pilih kategori produk')) !!}
                        <span class="form-text {{isset($errors->messages()['id_categories']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['id_categories']) ? $errors->messages()['id_categories'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Status:</strong><br>
                        {!! Form::radio('status', 1, array('class' => 'form-control form-control-sm')) !!} Aktif &nbsp;
                        {!! Form::radio('status', 0, array('class' => 'form-control form-control-sm')) !!} Tidak Aktif &nbsp;
                        <span class="form-text {{isset($errors->messages()['status']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['status']) ? $errors->messages()['status'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Harga  </strong>{{-- <i class="text-help text-danger">(sisa karakter <%= 100-title.length %>)</i> --}}
                        {!! Form::number('price', null, array('placeholder' => 'Harga', 'ng-trim'=>'false', 'maxlength'=>'100', /* 'ng-model'=>'title', */ 'id'=>'title', 'class' => 'form-control form-control-sm editable', 'maxlength'=>'100')) !!}
                        <span class="form-text {{isset($errors->messages()['price']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['price']) ? $errors->messages()['price'][0] .'*' : 'Harga produk wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <button type="submit" class="btn btn-sm btn-success">Simpan</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
$(document).ready(function() {

});
</script>
@endpush
@endsection
