@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('prestasi.edit', $getSurat) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Ubah Prestasi</div>
                <a class="btn btn-sm btn-warning" href="{{route('prestasi.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($getSurat, ['method' => 'PATCH','route' => ['prestasi.update', $getSurat->id], 'enctype' => 'multipart/form-data']) !!}
            <div class="row">
                @if($currentUser->getRoleNames()[0]=='Super Admin')
                    <div class="col-md-12">
                        <div class="form-group col-md-6">
                            <strong>Nama Taruna:</strong>
                            <select class="taruna form-control form-control-sm" name="id_user">
                            @if($selectTaruna)<option value="{{$selectTaruna->id}}" selected>{{$selectTaruna->name}}</option>@endif
                                <option value="">Pilih Taruna</option>
                            </select>
                            <span class="form-text {{isset($errors->messages()['id_user']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['id_user']) ? $errors->messages()['id_user'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="id_user" value="{{$selectTaruna->id}}">
                @else
                    <input type="hidden" name="id_user" value="{{$selectTaruna->id}}">
                @endif
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Waktu:</strong>
                        @php isset($errors->messages()['waktu']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('waktu', null, array('placeholder' => 'Waktu','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['waktu']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['waktu']) ? $errors->messages()['waktu'][0] .'*' : 'Waktu wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Tingkat:</strong>
                        @php isset($errors->messages()['tingkat']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('tingkat', null, array('placeholder' => 'tingkat','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['tingkat']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['tingkat']) ? $errors->messages()['tingkat'][0] .'*' : 'Tingkat wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Tempat:</strong>
                        @php isset($errors->messages()['tempat']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('tempat', null, array('placeholder' => 'tempat','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['tempat']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['tempat']) ? $errors->messages()['tempat'][0] .'*' : 'Tempat wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Keterangan:</strong>
                        @php isset($errors->messages()['keterangan']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('keterangan', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'keterangan')) !!}
                        <span class="form-text {{isset($errors->messages()['keterangan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['keterangan']) ? $errors->messages()['keterangan'][0] .'*' : 'Keterangan wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6" ng-controller="SelectFileController">
                        <strong>Lampiran:</strong><br>
                        <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                        <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:400px" /></div>
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Lampiran tidak wajib diisi'}}
                        </span>
                        @if($getSurat->photo)<img src="{{URL::to('/')}}/storage/{{config('app.documentImagePath')}}/prestasi/{{$getSurat->photo}}" height="50"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$getSurat->photo}}', '{{$getSurat->id}}')"> x </span> @endif
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
@push('scripts')
<script type="text/javascript">
    function deleteExist(fileName, id) {
        let deleteUrl = 'deleteExistImagePrestasi';
        let token ="{{csrf_token()}}";
        let params = {
           'image':fileName, 'id':id, "_token": token,
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
</script>
@endpush
@endsection
