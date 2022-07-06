<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Portal PT.YMPI</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ asset("css/login-style.css")}}">
  <link rel="stylesheet" href="{{ asset("css/bootstrap4.min.css")}}" id="bootstrap-css">
  <link rel="stylesheet" href="{{ asset("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/Ionicons/css/ionicons.min.css")}}">

</head>
<body class="hold-transition login-page">
  <div class="limiter">
    <div class="container-login100">
      <div class="wrap-login100">
        <div class="col-md-12 pl-md-5" style="margin-top: 20px;">
          <center><img src="{{url('logo_mirai_full.png')}}" style="width:300px;padding-bottom: 0px;"> </center>

          <form method="post" action="{{ route('login') }}">

          {{ csrf_field() }}
          <span class="login100-form-title"  style="color: white;background-color: #605ca8;padding-bottom: 0;text-align: center;font-size: 20px;font-weight: bold;padding: 10px;border-radius: 16px;margin-top: 20px;">
              Portal PT. YMPI
          </span>

          @if ($errors->has('username'))
                <div class="alert alert-danger alert-dismissible">
                    <h4 style="font-size: 15px;font-weight: bold;"> Error!</h4>
                    <span style="font-size: 12px">These credentials do not match our records.</span>
                </div>   
          @endif
          @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <h4 style="font-size: 15px;font-weight: bold;"> Success!</h4>
                    <span style="font-size: 12px">{{ session('success') }}</span>
                </div>   
            @endif

          <div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz" style="margin-top:20px">
            <input autocomplete="off" type="text" class="input100" placeholder="Username" id="username" name="username" value="{{ old('username') }}" required autofocus>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>
          </div>

          <div class="wrap-input100 validate-input" data-validate = "Password is required">
            <input class="input100" type="password" placeholder="Password" id="password" name="password" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
          </div>



          <div class="container-login100-form-btn">
            <button class="login100-form-btn" type="submit">
              Login
            </button>
          </div>

          <div class="text-center p-t-12">
            <span class="txt1">
              Forgot
            </span>
            <a class="txt2" href="{{url('forgot/password')}}">
              Username / Password?
            </a>
          </div>

          <div class="text-center p-t-20">
            <a class="txt2" href="{{url('register')}}">
              <b>Create your Account</b> 
              <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
            </a>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ asset('js/bootstrap4.js')}}"></script>
  <script src="{{ asset('js/jquery.min.js')}}"></script>
  <script src="{{ asset("plugins/iCheck/icheck.min.js")}}"></script>
  <script>
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
      });
    });

    jQuery(document).ready(function() {
      $('#username').val('');
      $('#password').val('');
    });
  </script>
</body>
</html>