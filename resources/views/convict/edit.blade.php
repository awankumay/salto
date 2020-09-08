@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('convict.edit', $convict) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Ubah Tahanan</div>
                <div class="p-2">
                    <a class="btn btn-sm btn-success float-right" href="{{route('convict.index')}}">Kembali</a></div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($convict, ['method' => 'PATCH', 'route' => ['convict.update', $convict->id], 'enctype' => 'multipart/form-data']) !!}
            <div class="row">
                <div class="col-lg-8 col-sm-12 col-md-12">
                    <div class="form-group col-md-12">
                        <strong>Nama  </strong>{{-- <i class="text-help text-danger">(sisa karakter <%= 100-title.length %>)</i> --}}
                        {!! Form::text('name', null, array('placeholder' => 'Nama tahanan', 'ng-trim'=>'false', 'maxlength'=>'100', /* 'ng-model'=>'title', */ 'id'=>'title', 'class' => 'form-control form-control-sm editable', 'maxlength'=>'100')) !!}
                        <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Nama tahanan wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12" ng-controller="SelectFileController">
                        <strong>Foto</strong>
                        <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                        <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:200px" /></div>
                        @if($convict->photo)<div ng-if="PreviewImage == null"> <img src="{{URL::to('/')}}/storage/{{config('app.convictImagePath')}}/{{$convict->photo}}" class="img img-fluid" style="width:200px;height:200px;"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$convict->photo}}', '{{$convict->id}}', 'image')"> x </span> </div>@endif
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Ukuran foto < 300kb *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Kategori:</strong>
                        {!! Form::select('type_convict', $type_convict, $select_type, array('class' => 'form-control form-control-sm','single', 'placeholder'=>'Pilih kategori tahanan')) !!}
                        <span class="form-text {{isset($errors->messages()['type_convict']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['type_convict']) ? $errors->messages()['type_convict'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12" ng-controller="SelectFileController">
                        <strong>Dokumen</strong>
                        <input type="file" name="file_2">
                        @if($convict->document)<div ng-if="PreviewImage == null"> <a href="{{URL::to('/')}}/storage/{{config('app.documentImagePath')}}/{{$convict->document}}" target="_blank"/>Lihat File</a><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$convict->document}}', '{{$convict->id}}', 'document')"> x </span> </div>@endif
                        <span class="form-text {{isset($errors->messages()['file_2']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file_2']) ? $errors->messages()['file_2'][0] .'*' : 'Ukuran dokumen < 500kb *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Blok / Kamar Hunian</strong>
                        {!! Form::text('lockup', null, array('placeholder' => 'Blok / Kamar Hunian', 'ng-trim'=>'false', 'maxlength'=>'100', /* 'ng-model'=>'title', */ 'id'=>'title', 'class' => 'form-control form-control-sm editable', 'maxlength'=>'100')) !!}
                        <span class="form-text {{isset($errors->messages()['lockup']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['lockup']) ? $errors->messages()['lockup'][0] .'*' : 'Kamar tahanan wajib diisi '}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Pelanggaran</strong>
                        {!! Form::text('violation', null, array('placeholder' => 'Pelanggaran', 'ng-trim'=>'false', 'maxlength'=>'100', /* 'ng-model'=>'title', */ 'id'=>'title', 'class' => 'form-control form-control-sm editable', 'maxlength'=>'100')) !!}
                        <span class="form-text {{isset($errors->messages()['violation']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['violation']) ? $errors->messages()['violation'][0] .'*' : 'Pelanggaran tahanan wajib diisi '}}
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
function deleteExist(fileName, id, type) {
    let deleteUrl = 'deleteExistImageConvict';
    let token ="{{csrf_token()}}";
    if(type=='image'){
        let params = {
            'image':fileName, 'id':id, "_token": token,
        }
    }else{
        let params = {
            'document':fileName, 'id':id, "_token": token,
        }
    }

    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true
    }).then((willDelete) => {
            if (willDelete) {
                $(document).ajaxSend(function() {
                    $("#overlay").fadeIn(300);　
                });
                $.ajax({
                    url: "{{url('/')}}/dashboard/"+deleteUrl,
                    type: 'POST',
                    data: params,
                    success:function(){
                        window.location.reload(true);
                    },
                    error:function(){
                        setTimeout(function(){
                            $("#overlay").fadeOut(300);
                            toastr.error("Error, image deleted failure");
                        },500);
                    }
                });
            } else {
                //swal("Your imaginary file is safe!");
            }
    });
}
$(document).ready(function() {
});
</script>
@endpush
@endsection
