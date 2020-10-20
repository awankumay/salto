<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="app">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Sistem Pelayanan Taruna dan Orang Tua (SALTO).') }}</title>

    @php /*Scripts
    <script src="{{ asset('js/app.js') }}" defer></script>*/@endphp

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{URL::asset('css/sb-admin-2.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('vendor/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{URL::asset('css/toastr.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('css/style.css')}}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <link rel="stylesheet" href="{{URL::asset('css/rowReorder.dataTables.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/responsive.dataTables.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/summernote-bs4.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/select2.min.css')}}">
    <link rel="stylesheet" href="{{URL::asset('css/bootstrap-datetimepicker.min.css')}}">

    <style>

    </style>
</head>
<body>
    <div id="app" ng-app="app">
        @guest
        @else
        <div id="overlay">
            <div class="cv-spinner">
                <span class="spinner"></span>
            </div>
        </div>
        <div id="wrapper">
            <ul class="navbar-nav bg-gradient-danger sidebar sidebar-dark accordion toggled" id="accordionSidebar">
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('home')}}">
                    <div class="sidebar-brand-icon">
                    <img src="/logo.png" class="img-fluid mx-auto d-block" style="max-height:80px;">
                    </div>
                    <div class="sidebar-brand-text mx-3">SALTO</div>
                </a>
                <hr class="sidebar-divider my-0">
                <li class="nav-item active">
                    <a class="nav-link" href="{{route('home')}}">
                    <i class="fa fa-university"></i>
                    <span>Dashboard</span></a>
                </li>
                @if(auth()->user()->hasPermissionTo('role-list') || auth()->user()->hasPermissionTo('user-list'))
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Hak Akses
                </div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        <i class="fas fa-fw fa-user-circle"></i>
                        <span>Pengaturan Hak Akses</span>
                    </a>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu</h6>
                        @if(auth()->user()->hasPermissionTo('role-list')) <a class="collapse-item" href="{{route('role.index')}}">Hak Akses</a> @endif
                        @if(auth()->user()->hasPermissionTo('user-list')) <a class="collapse-item" href="{{route('user.index')}}">Pengguna</a> @endif

                        </div>
                    </div>
                </li>
                @endif
                @if(auth()->user()->hasPermissionTo('kategori-surat-izin-list') || auth()->user()->hasPermissionTo('user-list'))
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Master Data
                </div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#master-data-salto" aria-expanded="true" aria-controls="master-data-salto">
                        <i class="fas fa-newspaper"></i>
                        <span>Master Data</span>
                    </a>
                    <div id="master-data-salto" class="collapse" aria-labelledby="master-data-salto" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu</h6>

                        @if(auth()->user()->hasPermissionTo('kategori-surat-izin-list'))<a class="collapse-item" href="{{route('permission.index')}}">Kategori Surat</a>@endif
                        @if(auth()->user()->hasPermissionTo('data-keluarga-asuh-list'))<a class="collapse-item" href="{{route('content.index')}}">Data Keluarga Asuh dan Taruna</a>@endif
                        @if(auth()->user()->hasPermissionTo('data-pembina-keluarga-asuh-list'))<a class="collapse-item" href="{{route('slider.index')}}">Data Wali Asuh dan Keluarga Asuh</a>@endif
                        </div>
                    </div>
                </li>
                @endif

                @if(auth()->user()->hasPermissionTo('surat-izin-orang-tua-sakit-list'))
                <hr class="sidebar-divider">
                <li class="nav-item">
                    <a class="nav-link" href="{{route('home')}}"> <i class="fas fa-newspaper"></i> <span>Surat Izin</span></a>
                </li>
                @endif
                @if(auth()->user()->hasPermissionTo('prestasi-taruna-list') || auth()->user()->hasPermissionTo('hukuman-dinas-list') || auth()->user()->hasPermissionTo('pengasuhan-daring-list') || auth()->user()->hasPermissionTo('surat-keterangan-list') || auth()->user()->hasPermissionTo('absensi-list') || auth()->user()->hasPermissionTo('jurnal-harian-list'))
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Monitoring
                </div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#data-monitoring" aria-expanded="true" aria-controls="data-monitoring">
                        <i class="fas fa-newspaper"></i>
                        <span>Monitoring</span>
                    </a>
                    <div id="data-monitoring" class="collapse" aria-labelledby="data-monitoring" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu</h6>
                        @if(auth()->user()->hasPermissionTo('prestasi-taruna-list'))<a class="collapse-item" href="{{route('post-category.index')}}">Penghargaan Taruna</a>@endif
                        @if(auth()->user()->hasPermissionTo('hukuman-dinas-list'))<a class="collapse-item" href="{{route('post-category.index')}}">Hukuman Dinas</a>@endif
                        @if(auth()->user()->hasPermissionTo('pengasuhan-daring-list'))<a class="collapse-item" href="{{route('content.index')}}">Pengasuhan Daring</a>@endif
                        @if(auth()->user()->hasPermissionTo('surat-keterangan-list'))<a class="collapse-item" href="{{route('content.index')}}">Surat Keterangan</a>@endif
                        @if(auth()->user()->hasPermissionTo('absensi-list'))<a class="collapse-item" href="{{route('slider.index')}}">Absensi Harian</a>@endif
                        @if(auth()->user()->hasPermissionTo('jurnal-harian-list'))<a class="collapse-item" href="{{route('slider.index')}}">Jurnal Harian</a>@endif
                        </div>
                    </div>
                </li>
                @endif
                @if(auth()->user()->hasPermissionTo('kategori-berita-list') || auth()->user()->hasPermissionTo('berita-list') || auth()->user()->hasPermissionTo('banner-list'))
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Berita & Informasi
                </div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#supplier" aria-expanded="true" aria-controls="supplier">
                        <i class="fas fa-newspaper"></i>
                        <span>Berita & Informasi</span>
                    </a>
                    <div id="supplier" class="collapse" aria-labelledby="supplier" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu</h6>

                        @if(auth()->user()->hasPermissionTo('kategori-berita-list'))<a class="collapse-item" href="{{route('post-category.index')}}">Kategori</a>@endif
                        @if(auth()->user()->hasPermissionTo('berita-list'))<a class="collapse-item" href="{{route('content.index')}}">Konten</a>@endif
                        @if(auth()->user()->hasPermissionTo('banner-list'))<a class="collapse-item" href="{{route('slider.index')}}">Banner</a>@endif
                        </div>
                    </div>
                </li>
                @endif
                @if(auth()->user()->hasPermissionTo('pengaduan-wbs-list'))
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Pengaduan
                </div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#pengaduan" aria-expanded="true" aria-controls="pengaduan">
                        <i class="fas fa-newspaper"></i>
                        <span>Pengaduan</span>
                    </a>
                    <div id="pengaduan" class="collapse" aria-labelledby="pengaduan" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu</h6>
                        @if(auth()->user()->hasPermissionTo('pengaduan-wbs-list'))<a class="collapse-item" href="{{route('report.index')}}">Pengaduan (WBS)</a>@endif
                        </div>
                    </div>
                </li>
                @endif
                <hr class="sidebar-divider">
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            </ul>
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <div id="page-top"></div>
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                        <span class="text-uppercase text-center" style="width:100%">Politeknik Ilmu Pemasyarakatan</span>
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown no-arrow mx-1">
                              <!--   <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fas fa-bell fa-fw"></i> -->
                                  <!-- Counter - Alerts -->
                                 <!--  <span class="badge badge-danger badge-counter">3+</span>
                                </a> -->
                                <!-- Dropdown - Alerts -->
                                <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                                  <h6 class="dropdown-header nope-dropdown-header">
                                    Alerts Center
                                  </h6>
                                  <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                      <div class="icon-circle bg-primary">
                                        <i class="fas fa-file-alt text-white"></i>
                                      </div>
                                    </div>
                                    <div>
                                      <div class="small text-gray-500">December 12, 2019</div>
                                      <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                  </a>
                                  <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                      <div class="icon-circle bg-success">
                                        <i class="fas fa-donate text-white"></i>
                                      </div>
                                    </div>
                                    <div>
                                      <div class="small text-gray-500">December 7, 2019</div>
                                      $290.29 has been deposited into your account!
                                    </div>
                                  </a>
                                  <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                      <div class="icon-circle bg-warning">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                      </div>
                                    </div>
                                    <div>
                                      <div class="small text-gray-500">December 2, 2019</div>
                                      Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                  </a>
                                  <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                                </div>
                              </li>
                            <div class="topbar-divider d-none d-sm-block"></div>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    @endguest
                    @yield('content')
                    @guest
                    @else

                </div>
                <footer class="sticky-footer" style="background:#f8f9fc;">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span> Copyright © Politeknik Ilmu Pemasyarakatan Tahun {{date('Y')}}. </span>
                        </div>
                    </div>
                </footer>
            </div>
            <a class="scroll-to-top rounded" href="#page-top">
                <i class="fas fa-angle-up"></i>
            </a>
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            <a class="btn btn-primary" href="login.html">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endguest
    </div>
    <script src="{{URL::asset('vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{URL::asset('vendor/jquery/jquery-2.1.3.min.js')}}"></script>
    <script src="{{URL::asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{URL::asset('vendor/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{URL::asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>
    <script src="{{URL::asset('js/sb-admin-2.min.js')}}"></script>
    <script src="{{URL::asset('vendor/chart.js/Chart.min.js')}}"></script>
    <script src="{{URL::asset('vendor/select2/js/select2.min.js')}}"></script>
    <script src="{{URL::asset('js/toastr.min.js')}}"></script>
    <script src="{{URL::asset('js/sweetalert.min.js')}}"></script>
    <script src="{{URL::asset('js/dataTables.rowReorder.min.js')}}"></script>
    <script src="{{URL::asset('js/dataTables.responsive.min.js')}}"></script>
    <script src="{{URL::asset('summernote/summernote-bs4.min.js')}}"></script>
    <script src="{{URL::asset('js/summernote-cleaner.js')}}"></script>
    <script src="{{URL::asset('js/my-app.js')}}"></script>
    <script src="{{URL::asset('js/select2.min.js')}}"></script>
    <script src="{{URL::asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{URL::asset('js/jquery.number.js')}}"></script>

    <script>
    $('.breadcrumb li a').each(function(){

    var breadWidth = $(this).width();

    if($(this).parent('li').hasClass('active') || $(this).parent('li').hasClass('first')){

    } else {

        $(this).css('width', 75 + 'px');

        $(this).mouseover(function(){
            $(this).css('width', breadWidth + 'px');
        });

        $(this).mouseout(function(){
            $(this).css('width', 75 + 'px');
        });
    }


    });
    @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
    @endif


    @if(Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
    @endif


    @if(Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
    @endif


    @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
    @endif


    </script>
    @stack('scripts')
</body>
</html>
