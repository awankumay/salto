@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('pengaduan') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tambah Pengaduan baru</div>
                <div class="p-2">
                    <a class="btn btn-sm btn-success float-right" href="{{route('report.index')}}">Kembali</a></div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['reportwbs.store'],'method'=>'POST')) !!}
                <input type="hidden" name="id_user" value="{{ auth()->user()->id }}">
                <input type="hidden" name="materi" id="materi_name" value="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Materi WBS:</strong>
                            @php isset($errors->messages()['id_materi']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::select('id_materi', $materi, null, ['placeholder' => 'Pilih Materi', 'id' => 'materi', 'class' => 'form-control form-control-sm '.$x.'', 'required' => 'required']) !!}
                            <span class="form-text {{isset($errors->messages()['id_materi']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['id_materi']) ? $errors->messages()['id_materi'][0] .'*' : 'Materi wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </div>
                <diw class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>What:</strong>
                            @php isset($errors->messages()['ewhat']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('ewhat', null, array('placeholder' => 'what','class' => 'form-control form-control-sm '.$x.'', 'required' => 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['ewhat']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['ewhat']) ? $errors->messages()['ewhat'][0] .'*' : 'Data wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </diw>
                <diw class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Who:</strong>
                            @php isset($errors->messages()['ewho']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('ewho', null, array('placeholder' => 'Who','class' => 'form-control form-control-sm '.$x.'', 'required' => 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['ewho']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['ewho']) ? $errors->messages()['ewho'][0] .'*' : 'Data wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </diw>
                <diw class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Why:</strong>
                            @php isset($errors->messages()['ewhy']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('ewhy', null, array('placeholder' => 'Why','class' => 'form-control form-control-sm '.$x.'', 'required' => 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['ewhy']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['ewhy']) ? $errors->messages()['ewhy'][0] .'*' : 'Data wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </diw>
                <diw class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>When:</strong>
                            @php isset($errors->messages()['ewhen']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('ewhen', null, array('placeholder' => 'When','class' => 'form-control form-control-sm '.$x.'', 'required' => 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['ewhen']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['ewhen']) ? $errors->messages()['ewhen'][0] .'*' : 'Data wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </diw>
                <diw class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Where:</strong>
                            @php isset($errors->messages()['ewhere']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('ewhere', null, array('placeholder' => 'Where','class' => 'form-control form-control-sm '.$x.'', 'required' => 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['ewhere']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['ewhere']) ? $errors->messages()['ewhere'][0] .'*' : 'Data wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </diw>
                <diw class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>How:</strong>
                            @php isset($errors->messages()['ehow']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('ehow', null, array('placeholder' => 'How','class' => 'form-control form-control-sm '.$x.'', 'required' => 'required')) !!}
                            <span class="form-text {{isset($errors->messages()['ehow']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['ehow']) ? $errors->messages()['ehow'][0] .'*' : 'Data wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </diw>
                <diw class="row">
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="form-group">
                            <button type="submit" class="btn btn-sm btn-danger btn-block">Simpan</button>
                        </div>
                    </div>
                </diw>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    $("#materi").on('change', function() {

        if(this.value != '' || this.value != undefined) {
            $('#materi_name').val($("#materi option:selected").text());
        } else {
            $('#materi_name').val('');
        }
        
    })
});
</script>
@endpush
@endsection
