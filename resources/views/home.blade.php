@extends('layouts.app')

@section('content')
<div class="container-fluid">
<!--     <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="card border-left-default shadow h-100 py-2 col-md-12">
            <div class="card-body">
                <h1 class="h3 mb-0 text-gray-800 text-uppercase text-center"> <img src="/logo.png" class="img-fluid" style="max-height:120px;margin:10px;">Politeknik Ilmu Pemasyarakatan</h1>
            </div>
        </div>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div> -->
   <div class="row justify-content-center">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Taruna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-taruna"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Orang Tua</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-orang-tua">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Wali Asuh</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800" id="total-waliasuh">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pembina</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-pembina">-</div>
                            </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Surat</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-surat">-</div>
                            </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Permintaan Surat Hari Ini</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-suratDateNow">-</div>
                            </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Surat Yang Belum Disetujui</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-suratPending">-</div>
                            </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(window).load(function(){
            $.get( "{{ url('/dashboard/totaluser') }}").done(function( res ) {
                $( "#total-taruna" ).html(res.data.total_taruna);
                $( "#total-orang-tua" ).html(res.data.total_orang_tua);
                $( "#total-waliasuh" ).html(res.data.total_waliasuh);
                $( "#total-pembina" ).html(res.data.total_pembina);
            });
            $.get( "{{ url('/dashboard/totalSurat') }}").done(function( res ) {
                $( "#total-surat" ).html(res.data.total_surat);
                $( "#total-suratPending" ).html(res.data.total_suratPending);
                $( "#total-suratDateNow" ).html(res.data.total_suratDateNow);
            });
        });
    </script>
@endpush('scripts')
@endsection
