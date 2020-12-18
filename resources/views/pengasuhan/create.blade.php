@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('pengasuhan.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah Pengasuhan</div>
                <a class="btn btn-sm btn-warning" href="{{route('pengasuhan.index')}}">Kembali</a>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['pengasuhan.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <input type="hidden" name="id_user" value="{{Auth::user()->id}}">
                        <input type="hidden" name="keluarga_asuh_id" value="{{$keluarga->id}}">
                        <input type="hidden" name="keluarga_asuh" value="{{$keluarga->name}}">
                        <strong>Judul:</strong>
                        @php isset($errors->messages()['judul']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('judul', null, array('placeholder' => 'Judul','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['judul']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['judul']) ? $errors->messages()['judul'][0] .'*' : 'Judul wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Media:</strong>
                        @php isset($errors->messages()['media']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('media', null, array('placeholder' => 'nama media','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['media']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['media']) ? $errors->messages()['media'][0] .'*' : 'Media wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>ID Media:</strong>
                        @php isset($errors->messages()['id_media']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('id_media', null, array('placeholder' => 'id media','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['id_media']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['id_media']) ? $errors->messages()['id_media'][0] .'*' : 'ID Media wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Password:</strong>
                        @php isset($errors->messages()['password']) ? $x='is-invalid' : $x='' @endphp
                        {!! Form::text('password', null, array('placeholder' => 'password','class' => 'form-control form-control-sm '.$x.'')) !!}
                        <span class="form-text {{isset($errors->messages()['password']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['password']) ? $errors->messages()['password'][0] .'*' : 'Password tidak wajib diisi'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>Start Time:</strong>
                        @php isset($errors->messages()['start_time']) ? $x='is-invalid' : $x='' @endphp
                        <input type="datetime-local" class="form-control form-control-sm $x" name="start_time">
                        <span class="form-text {{isset($errors->messages()['start_time']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['start_time']) ? $errors->messages()['start_time'][0] .'*' : 'Start Time wajib diisi *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-6">
                        <strong>End Time:</strong>
                        @php isset($errors->messages()['end_time']) ? $x='is-invalid' : $x='' @endphp
                        <input type="datetime-local" class="form-control form-control-sm $x" name="end_time">
                        <span class="form-text {{isset($errors->messages()['end_time']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['end_time']) ? $errors->messages()['end_time'][0] .'*' : 'End Time wajib diisi *'}}
                        </span>
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
@endsection
