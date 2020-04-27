<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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

    <style>

    </style>
</head>
<body>
    <div id="app">
        @guest
        @else
        <div id="overlay">
            <div class="cv-spinner">
                <span class="spinner"></span>
            </div>
        </div>
        <div id="wrapper">
            <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion toggled" id="accordionSidebar">
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('home')}}">
                    <div class="sidebar-brand-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3">Resto</div>
                </a>
                <hr class="sidebar-divider my-0">
                <li class="nav-item active">
                    <a class="nav-link" href="{{route('home')}}">
                    <i class="fas fa-fw fa-funnel-dollar"></i>
                    <span>Dashboard</span></a>
                </li>
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Privilages
                </div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        <i class="fas fa-fw fa-user-circle"></i>
                        <span>Master Privilages</span>
                    </a>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu</h6>
                        @if(auth()->user()->hasPermissionTo('role-list')) <a class="collapse-item" href="{{route('role.index')}}">Roles</a> @endif
                        @if(auth()->user()->hasPermissionTo('user-list')) <a class="collapse-item" href="{{route('user.index')}}">Users</a> @endif

                        </div>
                    </div>
                </li>
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    Requirement
                </div>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#supplier" aria-expanded="true" aria-controls="supplier">
                        <i class="fas fa-fw fa-user-circle"></i>
                        <span>Master Data</span>
                    </a>
                    <div id="supplier" class="collapse" aria-labelledby="supplier" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu</h6>
                        @if(auth()->user()->hasPermissionTo('product-category-list'))<a class="collapse-item" href="{{route('product-category.index')}}">Product Category</a>@endif
                        @if(auth()->user()->hasPermissionTo('product-list'))<a class="collapse-item" href="{{route('product.index')}}">Product</a>@endif
                        @if(auth()->user()->hasPermissionTo('store-list'))<a class="collapse-item" href="{{route('store.index')}}">Store</a>@endif
                        </div>
                    </div>
                </li>
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
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown no-arrow mx-1">
                                <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fas fa-bell fa-fw"></i>
                                  <!-- Counter - Alerts -->
                                  <span class="badge badge-danger badge-counter">3+</span>
                                </a>
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
                            <span> © nopeTech. </span>
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
    <script src="{{URL::asset('js//toastr.min.js')}}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


    <script>


    @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
            @php Session::forget('success') @endphp
    @endif


    @if(Session::has('info'))
            toastr.info("{{ Session::get('info') }}");
            @php Session::forget('info') @endphp
    @endif


    @if(Session::has('warning'))
            toastr.warning("{{ Session::get('warning') }}");
            @php Session::forget('warning') @endphp
    @endif


    @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
            @php Session::forget('error') @endphp
    @endif


    </script>
    @stack('scripts')
</body>
</html>
