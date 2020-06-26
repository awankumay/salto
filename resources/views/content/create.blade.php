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
                <div class="col-md-4">
                    <div class="form-group col-md-12">
                        <strong>Judul:</strong>
                        {!! Form::text('title', null, array('placeholder' => 'Judul','class' => 'form-control form-control-sm editable')) !!}
                        <span class="form-text {{isset($errors->messages()['title']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['title']) ? $errors->messages()['title'][0] .'*' : 'Judul wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Foto:</strong>
                        {!! Form::file('file', null, array('placeholder' => 'Foto','class' => 'form-control form-control-sm')) !!}
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
                </div>
                <div class="col-md-8">
                    <div class="form-group col-md-12">
                        <strong>Meta Title:</strong>
                        {!! Form::text('meta_title', null, array('placeholder' => 'Meta Title','class' => 'form-control form-control-sm editable')) !!}
                        <span class="form-text {{isset($errors->messages()['meta_title']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['meta_title']) ? $errors->messages()['meta_title'][0] .'*' : 'Meta title wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Meta Deskripsi:</strong>
                        {!! Form::text('meta_description', null, array('placeholder' => 'Meta Deskripsi','class' => 'form-control form-control-sm editable')) !!}
                        <span class="form-text {{isset($errors->messages()['meta_description']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['meta_description']) ? $errors->messages()['meta_description'][0] .'*' : 'Meta deskripsi wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Ringkasan:</strong>
                        {!! Form::textarea('excerpt', null, array('rows' => 3, 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Ringkasan')) !!}
                        <span class="form-text {{isset($errors->messages()['excerpt']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['excerpt']) ? $errors->messages()['excerpt'][0] .'*' : 'Ringkasan wajib disii *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Konten:</strong>
                        {!! Form::textarea('content', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Konten')) !!}
                        <span class="form-text {{isset($errors->messages()['content']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['content']) ? $errors->messages()['content'][0] .'*' : 'Konten wajib disii *'}}
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
<script>
var editor = new MediumEditor('.editable', {
    toolbar: {
        /* These are the default options for the toolbar,
           if nothing is passed this is what is used */
        allowMultiParagraphSelection: true,
        buttons: ['bold', 'italic', 'underline', 'anchor', 'h2', 'h3', 'quote'],
        diffLeft: 0,
        diffTop: -10,
        firstButtonClass: 'medium-editor-button-first',
        lastButtonClass: 'medium-editor-button-last',
        relativeContainer: null,
        standardizeSelectionStart: false,
        static: false,
        /* options which only apply when static is true */
        align: 'center',
        sticky: false,
        updateOnEmptySelection: false
    }
});
</script>
@endpush
@endsection
