<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
  <title>Portal PT. YMPI</title>
  <link rel="stylesheet" href="{{ url("css/bootstrap4.min.css")}}">
  <link rel="stylesheet" href="{{ url("css/dashboard/style.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/select2/dist/css/select2.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/Ionicons/css/ionicons.min.css")}}">
  @yield('stylesheets')
  <style>
    aside{
      font-size: 12px;
    }
  .crop {
    overflow: hidden;
  }
  .crop img {
    margin: -10% 0 -10% 0;
  }
    .sidebar-menu > li > a {
      padding: 7px 5px 7px 15px;
      display: block;
    }
    .treeview-menu > li > a {
      padding: 3px 5px 3px 15px;
      display: block;
      font-size: 12px;
    }
  </style>
  <style>
    aside{
      font-size: 12px;
    }
    .sidebar-menu > li > a {
      padding: 7px 5px 7px 15px;
      display: block;
    }
    .treeview-menu > li > a {
      padding: 3px 5px 3px 15px;
      display: block;
      font-size: 12px;
    }
  </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
  <div class="wrapper">
    @include('layouts.header')
    <div class="content-wrapper">
      @yield('header')
      <section class="content">
        <div class="error-page">
          <h2 class="headline text-yellow"> 404</h2>
          <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
            <p>
              Halaman yang anda kunjungi sedang dalam perbaikan.<br>
              Atau anda tidak memiliki hak akses ke halaman ini.<br><br>
              Tekan link di bawah ini untuk kembali ke halaman sebelumnya.<br>
              <a href="javascript:history.back()"><i class="fa fa-angle-double-left "></i> Kembali</a>
            </p>
            <p style="font-weight: bold; font-size:20px; color: red;">
              @if(isset($message))
              {{$message}}
              @else

              @endif
            </p>
          </div>
        </div>
      </div>
      @include('layouts.footer')
    </div>
    <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
    <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
    <script src="{{ asset('js/jquery.min.js')}}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('js/app-style-switcher.js')}}"></script>
    <script src="{{ asset('js/waves.js')}}"></script>
    <script src="{{ asset('js/sidebarmenu.js')}}"></script>
    <script src="{{ asset('js/custom.js')}}"></script>
    <script src="{{ asset('js/jquery.flot.js')}}"></script>
    <script src="{{ asset('js/dashboard1.js')}}"></script>
    <script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
    <script src="{{ url("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
    @yield('scripts')
  </body>
  </html>
