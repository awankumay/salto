@extends('layouts.app')
@section('content')
<div class="container bootstrap snippet">
    <div class="card">
        <div class="card-body">
            <div class="row">
            </div>
            <div class="row">

                <div class="col-sm-12">
                    <div class="">
                        <form class="form" action="{{route('setprofile')}}" method="POST" enctype="multipart/form-data">
                            @method('patch')
                            {{ csrf_field() }}
                            <div class="col-md-12">
                                <div class="form-group col-md-12">
                                    <strong>Profil Pengguna -  {{$data['name']}}</strong>
                                    <hr>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Foto:</strong>
                                    {!! Form::file('file', null, array('placeholder' => 'file','class' => 'form-control form-control-sm')) !!}
                                    <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Foto tidak wajib diisi'}}
                                    </span>
                                    @if($data['photo'])<img src="{{$data['photo']}}" height="150"/>@endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Nama:</strong>
                                    {!! Form::text('name', $data['name'], array('placeholder' => 'Nama','class' => 'form-control form-control-sm')) !!}
                                    <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Nama pengguna wajib diisi *'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Email:</strong>
                                    {!! Form::text('email', $data['email'], array('placeholder' => 'Email','class' => 'form-control form-control-sm')) !!}
                                    <span class="form-text {{isset($errors->messages()['email']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['email']) ? $errors->messages()['email'][0] .'*' : 'Email wajib diisi *'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Kata Sandi:</strong>
                                    {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control form-control-sm')) !!}
                                    <span class="form-text {{isset($errors->messages()['password']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['password']) ? $errors->messages()['password'][0] .'*' : 'Password wajib diisi *'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Konfirmasi Kata Sandi:</strong>
                                    {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control form-control-sm')) !!}
                                    <span class="form-text {{isset($errors->messages()['password']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['password']) ? $errors->messages()['password'][0] .'*' : 'Konfirmasi password *'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Nomor Telepon:</strong>
                                    {!! Form::number('phone', $data['phone'], array('placeholder' => 'Nomor telepon','class' => 'form-control form-control-sm')) !!}
                                    <span class="form-text {{isset($errors->messages()['phone']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['phone']) ? $errors->messages()['phone'][0] .'*' : 'Nomor telepon wajib diisi *'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Whatsapp:</strong>
                                    {!! Form::number('whatsapp', $data['whatsapp'], array('placeholder' => 'Whatsapp','class' => 'form-control form-control-sm')) !!}
                                    <span class="form-text {{isset($errors->messages()['whatsapp']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['whatsapp']) ? $errors->messages()['whatsapp'][0]  : 'Whatsapp tidak wajib diisi '}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Grade:</strong>
                                    {!! Form::select('grade', $data['grade_option'], $data['grade_select'], array('class' => 'form-control form-control-sm grade', 'single', 'placeholder'=>'Pilih Tingkat')) !!}
                                    <span class="form-text {{isset($errors->messages()['grade']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['grade']) ? $errors->messages()['grade'][0] .'' : 'Pilih Grade'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <label for="keluarga_asuh">Keluarga Asuh</label>
                                    <input type="text" class="form-control form-control-sm" name="keluarga_asuh" id="keluarga_asuh" placeholder="Keluarga Asuh" value="{{$data['keluarga_asuh']}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Jenis Kelamin:</strong>
                                    {!! Form::select('sex', ['1'=>'Laki-laki', '2'=>'Perempuan'], $data['sex'], array('class' => 'form-control form-control-sm grade', 'single', 'placeholder'=>'Pilih Jenis Kelamin')) !!}
                                    <span class="form-text {{isset($errors->messages()['sex']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['sex']) ? $errors->messages()['sex'][0] .'' : 'Pilih Jenis Kelamin * '}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <strong>Alamat:</strong>
                                    {!! Form::textarea('address', $data['alamat'], array('rows' => 3, 'cols' => 5, 'class'=>'form-control form-control-sm', 'placeholder'=>'Alamat pengguna')) !!}
                                    <span class="form-text {{isset($errors->messages()['address']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                        {{isset($errors->messages()['address']) ? $errors->messages()['address'][0] .'*' : 'Alamat tidak wajib diisi'}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group col-md-12">
                                    <button class="btn btn-sm btn-danger" type="submit"><i class="glyphicon glyphicon-ok-sign"></i> Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div><!--/tab-content-->
                </div><!--/col-9-->
            </div><!--/row-->
        </div>
    </div>
@endsection