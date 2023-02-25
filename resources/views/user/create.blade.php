@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('user.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah Pengguna</div>
                <a class="btn btn-sm btn-warning" href="{{route('user.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['user.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            {{ csrf_field() }}
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
                            <strong>STB:</strong>
                            {!! Form::text('stb', null, array('placeholder' => 'STB','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['stb']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['stb']) ? $errors->messages()['stb'][0] .'*' : 'STB pengguna wajib diisi *'}}
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
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Identitas:</strong>
                            {!! Form::text('identity', null, array('placeholder' => 'Identitas','class' => 'form-control form-control-sm')) !!}
                            <span class="form-text {{isset($errors->messages()['identity']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['identity']) ? $errors->messages()['identity'][0] .'*' : 'Identitas pengguna wajib diisi *'}}
                            </span>
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
                            {!! Form::radio('sex', '1', array('class' => 'form-control form-control-sm')) !!} Laki Laki &nbsp;
                            {!! Form::radio('sex', '2', array('class' => 'form-control form-control-sm')) !!} Perempuan &nbsp;
                            <span class="form-text {{isset($errors->messages()['sex']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['sex']) ? $errors->messages()['sex'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Provinsi:</strong>
                            {!! Form::select('province_id', $provinces, [], array('class' => 'form-control form-control-sm province', 'single', 'placeholder'=>'Pilih provinsi')) !!}
                            <span class="form-text {{isset($errors->messages()['province_id']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['province_id']) ? $errors->messages()['province_id'][0] .'' : 'Pilih provinsi'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Kota:</strong>
                            {!! Form::select('regencie_id', [], [], array('class' => 'form-control form-control-sm regencie', 'single', 'placeholder'=>'Pilih kota')) !!}
                            <span class="form-text {{isset($errors->messages()['regencie_id']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['regencie_id']) ? $errors->messages()['regencie_id'][0] .'' : 'Pilih kota'}}
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
                            {!! Form::select('role', $role, [], array('class' => 'form-control form-control-sm','single', 'placeholder'=>'Hak akses pengguna')) !!}
                            <span class="form-text {{isset($errors->messages()['role']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['role']) ? $errors->messages()['role'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Status:</strong><br>
                            {!! Form::radio('status', '1', array('class' => 'form-control form-control-sm')) !!} Active &nbsp;
                            {!! Form::radio('status', '0', array('class' => 'form-control form-control-sm')) !!} Not Active &nbsp;
                            <span class="form-text {{isset($errors->messages()['status']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['status']) ? $errors->messages()['status'][0] .'*' : 'Pilih salah satu *'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Grade:</strong>
                            {!! Form::select('grade', $grade, [], array('class' => 'form-control form-control-sm grade', 'single', 'placeholder'=>'Pilih Grade')) !!}
                            <span class="form-text {{isset($errors->messages()['grade']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['grade']) ? $errors->messages()['grade'][0] .'' : 'Pilih Grade'}}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <strong>Orang Tua:</strong>
                            {!! Form::select('orangtua', $orangtua, [], array('class' => 'form-control form-control-sm orangtua', 'single', 'placeholder'=>'Pilih Orang tua')) !!}
                            <span class="form-text {{isset($errors->messages()['orangtua']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['orangtua']) ? $errors->messages()['orangtua'][0] .'' : 'Pilih orang tua'}}
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
                    <button type="submit" class="btn btn-sm btn-danger">Save</button>
                </div>
            </div>
        </div>
            {!! Form::close() !!}
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    $(function(){
        $('.grade').select2();
        $('.orangtua').select2();
        $('.province').select2();
        $('.regencie').select2();
        $('.province').change(function () {
            var datax = [];
            $('.regencie').val(null);
            $('.regencie').empty();
            let province = $(this).val();
            let token ="{{csrf_token()}}";
            let params = {
                'province_id':province, '_token':token,
            }
            $.ajax({
                type: 'POST',
                url : "{{url('/')}}/dashboard/getregencies",
                data : params,
                success : function (res) {
                    $('.regencie').select2({data:JSON.parse(res)});
                    console.log(res);
                },
                error : function (res) {
                    console.log(res);
                }
            })
        });
    });

</script>
@endpush
@endsection