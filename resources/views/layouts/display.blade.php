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
      padding-left: 40px !important;
     }
    }
  </style>
</head>
<body class="hold-transition skin-purple sidebar-mini" data-sidebarbg="skin6" style="margin-bottom: 0px;height: 100%">
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="mini-sidebar"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <header class="topbar" data-navbarbg="skin6"> 
      <nav class="navbar top-navbar navbar-expand-md navbar-dark">
          <div class="navbar-header" data-logobg="skin6" style="background-color: #0b76b5;">
              <a class="navbar-brand" href="{{url('')}}">
                  <b class="logo-icon" id="logo-icon" style="padding-left: 40px">
                      <span class="logo-mini"><img src="{{ url("mirai_2.jpg")}}" height="50px" id="logo-yamaha" style="margin-bottom: 0px;padding: 0px"></span>
                  </b>
              </a>
              <a class="nav-toggler waves-effect waves-light text-dark d-block d-md-none"
                  href="{{url('')}}"><i class="ti-menu ti-close"></i></a>
          </div>
          <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
              
              <ul class="navbar-nav me-auto mt-md-0 ">
                  <li class="nav-item hidden-sm-down">
                      <form class="app-search ps-3" style="color: white">
                          <!-- <input type="text" class="form-control" placeholder="Search for..."> <a
                              class="srh-btn"><i class="ti-search"></i></a> -->
                              <?php if (ISSET($title)): ?>
                                <h4>{{$title}} (<span class="text-purple">{{$title_jp}}</span>)</h4>
                              <?php endif ?>
                      </form>
                  </li>
              </ul>

              <!-- <ul class="navbar-nav">
                  <li class="nav-item dropdown">

                      <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          {{Auth::user()->name}}
                      </a>
                      <ul class="dropdown-menu show" aria-labelledby="navbarDropdown"></ul>

                      <a class="btn btn-info btn-flat" href="{{ url("setting/user") }}" style="color:white">Setting</a>
                      <a class="btn btn-danger btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color:white">
                        Logout
                      </a>
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                      </form>
                        
                  </li>

                

              </ul> -->
          </div>
      </nav>
    </header>
    <div class="page-wrapper scroll-sidebar" style="background-color: rgb(60,60,60);margin-left: 0px">
      @yield('header')
      @yield('content')
    </div>
    @include('layouts.footer')
  </div>
  <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
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