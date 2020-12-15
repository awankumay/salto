@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('suket.edit', $getSurat) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Ubah Surat Keterangan</div>
                <a class="btn btn-sm btn-warning" href="{{route('suket.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($getSurat, ['method' => 'PATCH','route' => ['suket.update', $getSurat->id], 'enctype' => 'multipart/form-data']) !!}
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
                        <strong>Tempat & Tanggal Lahir:</strong>
                        @php isset($errors->messages()['ttl']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('ttl', $getSurat->ttl, array('placeholder' => 'Tempat & Tanggal Lahir','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['ttl']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['ttl']) ? $errors->messages()['ttl'][0] .'*' : 'Tempat tanggal lahir wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Pekerjaan:</strong>
                        @php isset($errors->messages()['pekerjaan']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('pekerjaan', $getSurat->pekerjaan, array('placeholder' => 'Pekerjaan','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['pekerjaan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['pekerjaan']) ? $errors->messages()['pekerjaan'][0] .'*' : 'Pekerjaan *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Orang Tua:</strong>
                        @php isset($errors->messages()['orangtua']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('orangtua', $getSurat->orangtua, array('placeholder' => 'orangtua','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['orangtua']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['orangtua']) ? $errors->messages()['orangtua'][0] .'*' : 'Orang Tua *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Keperluan:</strong>
                        @php isset($errors->messages()['keperluan']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('keperluan', $getSurat->keperluan, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'Keperluan')) !!}
                        <span class="form-text {{isset($errors->messages()['keperluan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['keperluan']) ? $errors->messages()['keperluan'][0] .'*' : 'Keperluan wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Alamat:</strong>
                        @php isset($errors->messages()['alamat']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::textarea('alamat', $getSurat->alamat, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm '.$x.'', 'placeholder'=>'alamat')) !!}
                        <span class="form-text {{isset($errors->messages()['alamat']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['alamat']) ? $errors->messages()['alamat'][0] .'*' : 'alamat wajib diisi *'}}
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
                        @if($getSurat->photo)<img src="{{URL::to('/')}}/storage/{{config('app.documentImagePath')}}/suket/{{$getSurat->photo}}" height="50"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$getSurat->photo}}', '{{$getSurat->id}}')"> x </span> @endif
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
        let deleteUrl = 'deleteExistImageSuket';
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
$(function () {
    //$('.kategori').select2();
    $(window).on('load', function () {
        var kategori = $('.kategori').val();
        switchForm(kategori);
    });
    $('.kategori').change(function () {
       var kategori = $('.kategori').val();
       switchForm(kategori);
    });
    function switchForm(kategori) {
        if(kategori==1){
            $('#izin-sakit').css('display', 'block');
            $("#izin-sakit :input").prop("disabled", false);
            $('#keluar-kampus').css('display', 'none');
            $("#keluar-kampus :input").prop("disabled", true);
            $('#training').css('display', 'none');
            $("#training :input").prop("disabled", true);
            $('#umum').css('display', 'none');
            $("#umum :input").prop("disabled", true);
        }else if(kategori==2){
            $('#izin-sakit').css('display', 'none');
            $("#izin-sakit :input").prop("disabled", true);
            $('#keluar-kampus').css('display', 'block');
            $("#keluar-kampus :input").prop("disabled", false);
            $('#training').css('display', 'none');
            $("#training :input").prop("disabled", true);
            $('#umum').css('display', 'none');
            $("#umum :input").prop("disabled", true);
        }else if(kategori==3){
            $('#izin-sakit').css('display', 'none');
            $("#izin-sakit :input").prop("disabled", true);
            $('#keluar-kampus').css('display', 'none');
            $("#keluar-kampus :input").prop("disabled", true);
            $('#training').css('display', 'block');
            $("#training :input").prop("disabled", false);
            $('#umum').css('display', 'none');
            $("#umum :input").prop("disabled", true);
        }else{
            $('#izin-sakit').css('display', 'none');
            $("#izin-sakit :input").prop("disabled", true);
            $('#keluar-kampus').css('display', 'none');
            $("#keluar-kampus :input").prop("disabled", true);
            $('#training').css('display', 'none');
            $("#training :input").prop("disabled", true);
            $('#umum').css('display', 'block');
            $("#umum :input").prop("disabled", false);
        }
        $('.kategori').attr('disabled', true);
        $('.taruna').attr('disabled', true);
    }
   /*  $('.taruna').select2({
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Masukan Nama Taruna',
        ajax: {
            dataType: 'json',
            url: '/dashboard/gettaruna',
            delay: 300,
            data: function(params) {
                return {
                    search: params.term
                }
            },
            processResults: function (data, page) {
                return {
                    results: data
                };
            },
        }
    }).on('taruna:select', function (evt) {
         var data = $(".taruna option:selected").text();
    }); */

});
</script>
@endpush
@endsection
