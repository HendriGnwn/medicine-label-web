<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('headerTitle', 'Main') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/buttons.dataTables.min.css') }}">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.css') }}" />
<!--    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.dat aTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" />-->
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <img src="{{ asset('files/rsmm-logo.png') }}" class="img-responsive" style="float:left;width:30px" />
                        <h4 style="float:right;margin-left:10px;margin-top:6px;">{{ config('app.name', 'Laravel') }}</h4>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                        @else
                            <li class="{{ \Request::is('home') ? 'active' : '' }}">
                                <a href="{{ route('home') }}">Dashboard</a>
                            </li>
                            <li class="{{ \Request::is('manually/create') ? 'active' : '' }}">
                                <a href="{{ route('manually.create') }}">Buat Baru</a>
                            </li>
                            @if (\Auth::user()->getIsRoleDoctor())
                            <li class="{{ \Request::is('transaction-medicine/doctor') ? 'active' : '' }}">
                                <a href="{{ route('transaction-medicine.doctor') }}">Daftar</a>
                            </li>
                            @endif
                            @if (\Auth::user()->getIsRolePharmacist())
                            <li class="{{ \Request::is('transaction-medicine/pharmacist') ? 'active' : '' }}">
                                <a href="{{ route('transaction-medicine.pharmacist') }}">Daftar Label</a>
                            </li>
                            <li class="{{ \Request::is('transaction-add-medicine/index') ? 'active' : '' }}">
                                <a href="{{ route('transaction-add-medicine.index') }}">Daftar dari SIMRS</a>
                            </li>
                            <li class="{{ \Request::is('report/index') ? 'active' : '' }}">
                                <a href="{{ route('report.index') }}">Laporan</a>
                            </li>
                            @endif
                            @if (\Auth::user()->getIsRoleSuperadmin())
                            <li class="{{ \Request::is('manually') ? 'active' : '' }}">
                                <a href="{{ route('manually.index') }}">Daftar Label</a>
                            </li>
                            <li class="{{ \Request::is('transaction-add-medicine/index') ? 'active' : '' }}">
                                <a href="{{ route('transaction-add-medicine.index') }}">Daftar dari SIMRS</a>
                            </li>
                            <li class="{{ \Request::is('report/index') ? 'active' : '' }}">
                                <a href="{{ route('report.index') }}">Laporan</a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                    Master <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('user.index') }}">User</a></li>
                                    <li><a href="{{ route('pharmacist.index') }}">Apoteker</a></li>
                                </ul>
                            </li>
                            @endif
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('user.edit-profile') }}">Edit Profile</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a
                    <strong>Fail!</strong> {{ session('error') }}
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a
                    <strong>Info!</strong> {{ session('info') }}
                </div>
            @endif
            @yield('content')
        </div>
        
    </div>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('vendor/moment/min/moment.min.js')}}"></script>
    <!-- DataTables -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/js/buttons.colVis.min.js') }}"></script>
    <!-- Bootstrap JavaScript -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js')}}"></script>
    <script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{ asset('vendor/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    @stack('script')
</body>
</html>
