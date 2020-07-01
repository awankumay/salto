@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('content.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah konten baru</div>
                <div class="p-2">
                    <a class="btn btn-sm btn-success float-right" href="{{route('content.index')}}">Kembali</a></div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['content.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                <div class="col-lg-8 col-sm-12 col-md-12">
                    <div class="form-group col-md-12">
                        <strong>Judul  </strong>{{-- <i class="text-help text-danger">(sisa karakter <%= 100-title.length %>)</i> --}}
                        {!! Form::text('title', null, array('placeholder' => 'Judul', 'ng-trim'=>'false', 'maxlength'=>'100', /* 'ng-model'=>'title', */ 'id'=>'title', 'class' => 'form-control form-control-sm editable', 'maxlength'=>'100')) !!}
                        <span class="form-text {{isset($errors->messages()['title']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['title']) ? $errors->messages()['title'][0] .'*' : 'Judul wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Ringkasan:</strong> {{-- <i class="text-help text-danger">(sisa karakter <%= 250-excerpt.length %>)</i> --}}
                        {!! Form::textarea('excerpt', null, array('rows' => 4, 'ng-model'=>'excerpt', 'maxlength'=>'250', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Ringkasan')) !!}
                        <span class="form-text {{isset($errors->messages()['excerpt']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['excerpt']) ? $errors->messages()['excerpt'][0] .'*' : 'Ringkasan wajib disii *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Konten:</strong>
                      {!! Form::textarea('content', null, array('id'=>'summernote', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Konten')) !!}
                        <span class="form-text {{isset($errors->messages()['content']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['content']) ? $errors->messages()['content'][0] .'*' : 'Konten wajib disii *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Tags:</strong>
                        {!! Form::select('tags[]', $tags, [], array('class' => 'form-control form-control-sm tagging', 'multiple', 'placeholder'=>'Pilih Tags')) !!}
                        <span class="form-text {{isset($errors->messages()['tags']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['tags']) ? $errors->messages()['tags'][0] .'' : 'Pilih tags'}}
                        </span>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="form-group col-md-12" ng-controller="SelectFileController">
                        <strong>Foto Utama</strong>
                        <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                        <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:200px" /></div>
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Ukuran foto < 100kb *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Kategori:</strong>
                        {!! Form::select('post_categories_id', $postCategory, [], array('class' => 'form-control form-control-sm','single', 'placeholder'=>'Pilih kategori')) !!}
                        <span class="form-text {{isset($errors->messages()['post_categories_id']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['post_categories_id']) ? $errors->messages()['post_categories_id'][0] .'*' : 'Pilih salah satu *'}}
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
                        <strong>Headline:</strong><br>
                        {!! Form::radio('headline', 1, array('class' => 'form-control form-control-sm')) !!} Ya &nbsp;
                        {!! Form::radio('headline', 0, array('class' => 'form-control form-control-sm')) !!} Tidak &nbsp;
                        <span class="form-text {{isset($errors->messages()['headline']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['headline']) ? $errors->messages()['headline'][0] .'*' : 'pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Meta Title</strong> {{-- <i class="text-help text-danger">(sisa karakter <%= 150-meta_description.length %>)</i> --}}
                        {!! Form::textarea('meta_title', null, array('rows' => 2, 'ng-model'=>'meta_title', 'maxlength'=>'150', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Meta Title')) !!}
                        <span class="form-text {{isset($errors->messages()['meta_title']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['meta_title']) ? $errors->messages()['meta_title'][0] .'*' : 'Meta title wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Meta Deskripsi</strong> {{-- <i class="text-help text-danger">(sisa karakter <%= 150-meta_description.length %>)</i> --}}
                        {!! Form::textarea('meta_description', null, array('rows' => 4, 'ng-model'=>'meta_description', 'maxlength'=>'150', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Meta Deskripsi')) !!}
                        <span class="form-text {{isset($errors->messages()['meta_description']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['meta_description']) ? $errors->messages()['meta_description'][0] .'*' : 'Meta deskripsi wajib diisi *'}}
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
    $('.tagging').select2();
    setTimeout(() => {
        $('#summernote').summernote({
            toolbar:[
                ['cleaner',['cleaner']], // The Button
                ['style',['style']],
                ['font',['bold','italic','underline','clear']],
                ['fontname',['fontname']],
                ['color',['color']],
                ['para',['ul','ol','paragraph']],
                ['height',['height']],
                ['table',['table']],
                ['insert',['picture','link','hr']],
                ['view',['fullscreen','codeview']],
            ],
            cleaner:{
                action: 'both', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
                newline: '<br>', // Summernote's default is to use '<p><br></p>'
                notStyle: 'position:absolute;top:0;left:0;right:0', // Position of Notification
                icon: '<i class="note-icon">[Reset]</i>',
                keepHtml: false, // Remove all Html formats
                keepOnlyTags: ['<p>', '<br>', '<ul>', '<li>', '<b>', '<strong>','<i>', '<a>'], // If keepHtml is true, remove all tags except these
                keepClasses: false, // Remove Classes
                badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'], // Remove full tags with contents
                badAttributes: ['style', 'start'], // Remove attributes from remaining tags
                limitChars: false, // 0/false|# 0/false disables option
                limitDisplay: 'both', // text|html|both
                limitStop: false // true/false
            },
            height: 200,
            callbacks: {
                onImageUpload: function(image) {
                    var sizeKB = image[0]['size'] / 1000;
                    var tmp_pr = 0;
                    if(sizeKB > 100){
                        tmp_pr = 1;
                        toastr.error("Gagal, Ukuran gambar maksimal 100kb");
                    }
                    if(image[0]['type'] != 'image/jpeg' && image[0]['type'] != 'image/png'){
                        tmp_pr = 1;
                        toastr.error("Gagal, Tipe file yang diizinkan jpeg/png");
                    }
                    if(tmp_pr == 0){
                        var file = image[0];
                        var reader = new FileReader();
                        reader.onloadend = function() {
                            var image = $('<img>').attr('src',  reader.result);
                            $('#summernote').summernote("insertNode", image[0]);
                        }
                    reader.readAsDataURL(file);
                    }
                }
            }
        });
    }, 200);

});
</script>
@endpush
@endsection
