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
            {!! Form::open(array('route' => ['report.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
                <input type="hidden" name="id_user" value="{{ auth()->user()->id }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Pesan Pengaduan:</strong>
                            @php isset($errors->messages()['pengaduan']) ? $x='is-invalid' : $x='' @endphp
                            {!! Form::text('pengaduan', null, array('placeholder' => 'Pesan Pengaduan','class' => 'form-control form-control-sm '.$x.'')) !!}
                            <span class="form-text {{isset($errors->messages()['pengaduan']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['pengaduan']) ? $errors->messages()['pengaduan'][0] .'*' : 'Pesan pengaduan wajib diisi *'}}
                            </span>
                        </div>
                    </div>
                </div>
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

});
</script>
@endpush
@endsection
