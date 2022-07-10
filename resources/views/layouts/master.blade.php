
<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
  <title>Bridge For Vendor</title>
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
    .text-red{
      color: red;
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
    @media (min-width:576px) {
     #logo-yamaha {
      height: 20px;
     }
     #logo-icon {
      padding-left: 0px !important;
     }
    }
    @media (min-width:767px) {
     #logo-yamaha {
      height: 20px !important;
     }
     #logo-icon {
      padding-left: 0px !important;
     }
    }
    @media (min-width:768px) {
     #logo-yamaha {
      height: 20px !important;
     }
     #logo-icon {
      padding-left: 0px !important;
     }
    }
    @media (min-width:1200px) {
     #logo-yamaha {
      height: 50px !important;
     }
     #logo-icon {
      padding-left: 20px !important;
     }
    }
  </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="mini-sidebar"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    @include('layouts.header')
    @include('layouts.leftbar')
    <div class="page-wrapper">
      @yield('header')
      @yield('content')
      @include('layouts.footer')
    </div>
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
  <script type="text/javascript">
    // jQuery(document).ready(function() {
    //   document.getElementById("main-wrapper").setAttribute('data-sidebartype','mini-sidebar');
    //   document.getElementById("logo-yamaha").setAttribute('height','20px');
    //   document.getElementById("logo-yamaha").style.setProperty('height', '20px', 'important');
    //   document.getElementsByClassName('logo-icon')[0].style.setProperty('padding-left', '0px', 'important');
    //   document.getElementsByClassName('hide-menu')[0].style.setProperty('display', 'none', 'important');
    //   document.getElementsByClassName('sidebar-item active selected')[0].style.setProperty('width', '65px', 'important');
    // });
    function sidebarCollapse() {
      if (document.getElementById("main-wrapper").getAttribute("data-sidebartype") == 'full') {
        document.getElementById("main-wrapper").setAttribute('data-sidebartype','mini-sidebar');
        document.getElementById("logo-yamaha").setAttribute('height','20px');
        document.getElementById("logo-yamaha").style.setProperty('height', '20px', 'important');
        document.getElementsByClassName('logo-icon')[0].style.setProperty('padding-left', '0px', 'important');
        document.getElementsByClassName('hide-menu')[0].style.setProperty('display', 'none', 'important');
        document.getElementsByClassName('sidebar-item active selected')[0].style.setProperty('width', '65px', 'important');
      }else{
        document.getElementById("main-wrapper").setAttribute('data-sidebartype','full');
        document.getElementById("logo-yamaha").setAttribute('height','50px');
        document.getElementById("logo-yamaha").style.setProperty('height', '50px', 'important');
        document.getElementsByClassName('logo-icon')[0].style.setProperty('padding-left', '40px', 'important');
        document.getElementsByClassName('hide-menu')[0].style.setProperty('display', 'block', 'important');
        document.getElementsByClassName('sidebar-item active selected')[0].style.setProperty('width', '240px', 'important');
      }
    }
  </script>
  @yield('scripts')
</body>
</html>