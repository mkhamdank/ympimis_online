<header class="topbar" data-navbarbg="skin6"> 
  <nav class="navbar top-navbar navbar-expand-md navbar-dark">
      <div class="navbar-header" data-logobg="skin6" style="background-color: #0b76b5;">
          <a class="navbar-brand" href="{{url('')}}">
            <center>
              <b class="logo-icon" style="padding-left: 20px" id="logo-icon">
                  <span class="logo-mini"><img src="{{ url("mirai_2.jpg")}}" height="50px" id="logo-yamaha" style="margin-bottom: 0px;padding: 0px"></span>
              </b>
            </center>
          </a>
          <!-- <a class="nav-toggler waves-effect waves-light text-dark d-block d-md-none"
              href="{{url('')}}"><i class="ti-menu ti-close"></i>
          </a> -->
      </div>
      <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
          <div class="col-md-1">
            <button class="btn" style="color: white;font-size: 20px" onclick="sidebarCollapse()"><i class="fa fa-bars"></i></button>
          </div>
          <ul class="navbar-nav me-auto mt-md-0 ">
              <li class="nav-item hidden-sm-down">
                  <form class="app-search ps-3" style="color: white">
                      <!-- <input type="text" class="form-control" placeholder="Search for..."> <a
                          class="srh-btn"><i class="ti-search"></i></a> -->
                          <!-- <?php if (ISSET($title)): ?>
                            <h4>{{$title}} (<span class="text-purple">{{$title_jp}}</span>)</h4>
                          <?php endif ?> -->
                  </form>
              </li>
          </ul>

          <ul class="navbar-nav">
              <li class="nav-item dropdown">

                  <a class="nav-link dropdown-toggle waves-effect waves-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      {{Auth::user()->name}}
                  </a>
                  <ul class="dropdown-menu show" aria-labelledby="navbarDropdown"></ul>

                  <a class="btn btn-info btn-flat" href="{{ url("setting/user") }}" style="color:white"><i class="fa fa-gear"></i> Account Setting</a>
                  <a class="btn btn-danger btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color:white">
                    Logout
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                  </form>
                    
              </li>

            

          </ul>
      </div>
  </nav>
</header>