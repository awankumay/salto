@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('user.edit', $user) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Ubah Pengguna</div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($user, ['method' => 'PATCH','route' => ['user.update', $user->id],  'enctype' => 'multipart/form-data']) !!}
            <div class="row">
                <div class="col-md-4">
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            Pendaftaran Pengguna
                            <hr>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Nama:</strong>
                            {!! Form::text('name', null, array('placeholder' => 'Nama','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Nama pengguna wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Email:</strong>
                            {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['email']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['email']) ? $errors->messages()['email'][0] .'*' : 'Email wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Kata Sandi:</strong>
                            {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['password']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['password']) ? $errors->messages()['password'][0] .'*' : 'Password wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Konfirmasi Kata Sandi:</strong>
                            {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['password']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['password']) ? $errors->messages()['password'][0] .'*' : 'Konfirmasi password *'}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            Informasi Pengguna
                            <hr>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Foto:</strong>
                            {!! Form::file('file', null, array('placeholder' => 'file','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Foto tidak wajib diisi'}}
                            </span>
                            @if($user->photo)<img src="{{URL::to('/')}}/storage/{{config('app.userImagePath')}}/{{$user->photo}}" height="50"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$user->photo}}', '{{$user->id}}')"> x </span> @endif
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Nomor Telepon:</strong>
                            {!! Form::number('phone', null, array('placeholder' => 'Nomor telepon','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['phone']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['phone']) ? $errors->messages()['phone'][0] .'*' : 'Nomor telepon wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Whatsapp:</strong>
                            {!! Form::number('whatsapp', null, array('placeholder' => 'Whatsapp','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['whatsapp']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['whatsapp']) ? $errors->messages()['whatsapp'][0]  : 'Whatsapp tidak wajib diisi '}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Jenis Kelamin:</strong><br>
                            {!! Form::radio('sex', '1', array('class' => 'form-control form-control-sm')) !!} Man &nbsp;
                            {!! Form::radio('sex', '2', array('class' => 'form-control form-control-sm')) !!} Women &nbsp;
                            <span class="form-text {{isset($errors->messages()['sex']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['sex']) ? $errors->messages()['sex'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Alamat:</strong>
                            {!! Form::textarea('address', null, array('rows' => 3, 'cols' => 5, 'class'=>'form-control form-control-sm', 'placeholder'=>'Alamat pengguna')) !!}
                            <span class="form-text {{isset($errors->messages()['address']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['address']) ? $errors->messages()['address'][0] .'*' : 'Alamat tidak wajib diisi'}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            Hak Akses
                            <hr>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Role:</strong>
                            {!! Form::select('role[]', $role, $userRole, array('class' => 'form-control form-control-sm','single', 'placeholder'=>'Hak akses pengguna')) !!}
                            <span class="form-text {{isset($errors->messages()['role']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['role']) ? $errors->messages()['role'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Status:</strong><br>
                            {!! Form::radio('status', '1', array('class' => 'form-control form-control-sm')) !!} Active &nbsp;
                            {!! Form::radio('status', '2', array('class' => 'form-control form-control-sm')) !!} Not Active &nbsp;
                            <span class="form-text {{isset($errors->messages()['status']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['status']) ? $errors->messages()['status'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                   {{--- <div class="col-md-12">
                        <div class="form-group col-md-12">
                            {!! Form::radio('privileges', 1, array('class' => 'form-control form-control-sm')) !!} Website &nbsp;
                            {!! Form::radio('privileges', 2, array('class' => 'form-control form-control-sm')) !!} Apps &nbsp;
                            {!! Form::radio('privileges', 3, array('class' => 'form-control form-control-sm')) !!} Both &nbsp;
                            <span class="form-text {{isset($errors->messages()['privileges']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['privileges']) ? $errors->messages()['privileges'][0] .'*' : 'choose user privileges *'}}
                            </span>
                        </div>
                    </div>---}}
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-6">
                <div class="form-group col-md-12">
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                    <a class="btn btn-sm btn-success" href="{{route('user.index')}}">Cancel</a>
                </div>
            </div>
        </div>
            {!! Form::close() !!}
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    function deleteExist(fileName, id) {
        let deleteUrl = 'deleteExistImageUser';
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
                            url: "{{url('/')}}/"+deleteUrl,
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
