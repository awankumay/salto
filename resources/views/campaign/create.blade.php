@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('campaign.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah campaign baru</div>
                <div class="p-2">
                    <a class="btn btn-sm btn-success float-right" href="{{route('campaign.index')}}">Kembali</a></div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['campaign.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                <div class="col-lg-8 col-sm-12 col-md-12">
                    <div class="form-group col-md-12">
                        <strong>Judul  </strong>
                        {!! Form::text('title', null, array('placeholder' => 'Judul', 'ng-trim'=>'false', 'maxlength'=>'100', 'id'=>'title', 'class' => 'form-control form-control-sm editable')) !!}
                        <span class="form-text {{isset($errors->messages()['title']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['title']) ? $errors->messages()['title'][0] .'*' : 'Judul wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Ringkasan:</strong>
                        {!! Form::textarea('excerpt', null, array('rows' => 3, 'maxlength'=>'250', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Ringkasan')) !!}
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
                        <div class="row">
                            <div class="form-group col-md-6">
                                <strong>Mulai:</strong><br>
                                {!! Form::date('date_started', null, array('id'=>'date_started', 'class' => 'form-control form-control-sm')) !!}
                                <span class="form-text {{isset($errors->messages()['date_started']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['date_started']) ? $errors->messages()['date_started'][0] .'*' : 'Tanggal mulai wajib diisi *'}}
                            </div>
                            <div class="form-group col-md-6">
                                <strong>Selesai:</strong><br>
                                {!! Form::date('date_ended', null, array('id'=>'date_ended', 'class' => 'form-control form-control-sm')) !!}
                                <span class="form-text {{isset($errors->messages()['date_ended']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['date_ended']) ? $errors->messages()['date_ended'][0] .'*' : 'Tanggal selesai wajib diisi *'}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <strong>Batasi kebutuhan dana:</strong><br>
                                {!! Form::radio('set_fund_target', 1, array('class' => 'form-control form-control-sm set_fund_target')) !!} Ya &nbsp;
                                {!! Form::radio('set_fund_target', 2, array('class' => 'form-control form-control-sm set_fund_target')) !!} Tidak &nbsp;
                                <span class="form-text {{isset($errors->messages()['set_fund_target']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['set_fund_target']) ? $errors->messages()['set_fund_target'][0] .'*' : 'pilih salah satu *'}}
                                </span>
                            </div>
                            <div class="form-group col-md-6">
                                <strong>Kebutuhan dana  </strong>
                                {!! Form::text('fund_target', null, array('placeholder' => '', 'ng-trim'=>'false', 'id'=>'fund_target', 'class' => 'form-control form-control-sm')) !!}
                                {!! Form::hidden('fund_target_value', null, array('placeholder' => '', 'ng-trim'=>'false', 'id'=>'fund_target_value', 'class' => 'form-control form-control-sm')) !!}
                                <span class="form-text {{isset($errors->messages()['fund_target']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['fund_target']) ? $errors->messages()['fund_target'][0] .'*' : 'Kebutuhan dana wajib diisi *'}}
                                </span>
                            </div>
                        </div>
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
                        <strong>Status:</strong><br>
                        {!! Form::radio('status', 1, array('class' => 'form-control form-control-sm')) !!} Aktif &nbsp;
                        {!! Form::radio('status', 2, array('class' => 'form-control form-control-sm')) !!} Tidak Aktif &nbsp;
                        <span class="form-text {{isset($errors->messages()['status']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['status']) ? $errors->messages()['status'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Headline:</strong><br>
                        {!! Form::radio('headline', 1, array('class' => 'form-control form-control-sm')) !!} Ya &nbsp;
                        {!! Form::radio('headline', 2, array('class' => 'form-control form-control-sm')) !!} Tidak &nbsp;
                        <span class="form-text {{isset($errors->messages()['headline']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['headline']) ? $errors->messages()['headline'][0] .'*' : 'pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Tags:</strong>
                        {!! Form::select('tags[]', $tags, [], array('class' => 'form-control form-control-sm tagging', 'multiple')) !!}
                        <span class="form-text {{isset($errors->messages()['tags']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['tags']) ? $errors->messages()['tags'][0] .'' : 'Pilih tags'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Meta Title</strong>
                        {!! Form::textarea('meta_title', null, array('rows' => 2, 'maxlength'=>'120', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Meta Title')) !!}
                        <span class="form-text {{isset($errors->messages()['meta_title']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['meta_title']) ? $errors->messages()['meta_title'][0] .'*' : 'Meta title wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Meta Deskripsi</strong>
                        {!! Form::textarea('meta_description', null, array('rows' => 4, 'maxlength'=>'200', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Meta Deskripsi')) !!}
                        <span class="form-text {{isset($errors->messages()['meta_description']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['meta_description']) ? $errors->messages()['meta_description'][0] .'*' : 'Meta deskripsi wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <strong>Pilih Akun penerima</strong><br>
                                {!! Form::select('beneficiary_account_issuer[]', $beneficiary_account_issuer, [], array('class' => 'form-control form-control-sm tagging', 'single', 'placeholder'=>'Pilih Akun')) !!}
                                <span class="form-text {{isset($errors->messages()['beneficiary_account_issuer']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['beneficiary_account_issuer']) ? $errors->messages()['beneficiary_account_issuer'][0] .'' : 'Pilih Akun * '}}
                                </span>
                            </div>
                            <div class="form-group col-md-12">
                                <strong>No.Akun Penerima</strong><br>
                                {!! Form::number('beneficiary_account', null, array('placeholder' => 'No Akun', 'ng-trim'=>'false', 'maxlength'=>'20', 'id'=>'beneficiary_account', 'class' => 'form-control form-control-sm editable')) !!}
                                <span class="form-text {{isset($errors->messages()['beneficiary_account']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['beneficiary_account']) ? $errors->messages()['beneficiary_account'][0] .'*' : 'No Rek wajib diisi *'}}
                                </span>
                            </div>
                            <div class="form-group col-md-12">
                                <strong>Nama Akun penerima</strong><br>
                                {!! Form::text('beneficiary_account_name', null, array('placeholder' => 'Nama Akun', 'ng-trim'=>'false', 'maxlength'=>'50', 'id'=>'beneficiary_account_name', 'class' => 'form-control form-control-sm editable')) !!}
                                <span class="form-text {{isset($errors->messages()['beneficiary_account_name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['beneficiary_account_name']) ? $errors->messages()['beneficiary_account_name'][0] .'*' : 'Nama akun wajib diisi *'}}
                                </span>
                            </div>
                        </div>
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
    $('#fund_target').number( true, 0);
    $('#fund_target').on('keyup',function(){
        var val = $('#fund_target').val();
        $('#fund_target_value').val(val);
	});
    $('input:radio[name="set_fund_target"]').change(
    function(){
        if (this.checked && this.value == '2') {
            $("#fund_target").prop("disabled", true);
            $('#fund_target_value').val();
            $('#fund_target').val();
        }else{
            $("#fund_target").prop("disabled", false);
        }
    });

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
