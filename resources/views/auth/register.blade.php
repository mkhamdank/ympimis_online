<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>YMPI Information System</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ asset("css/login-style.css")}}">
  <link rel="stylesheet" href="{{ asset("css/bootstrap4.min.css")}}" id="bootstrap-css">
  <link rel="stylesheet" href="{{ asset("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ asset("bower_components/Ionicons/css/ionicons.min.css")}}">


<style type="text/css">

  .containers {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 15px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    padding-top: 6px;
  }

  /* Hide the browser's default checkbox */
  .containers input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
  }


  .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
    margin-top: 4px;
  }

  /* On mouse-over, add a grey background color */
  .containers:hover input ~ .checkmark {
    background-color: #ccc;
  }

  /* When the checkbox is checked, add a blue background */
  .containers input:checked ~ .checkmark {
    background-color: #2196F3;
  }

  /* Create the checkmark/indicator (hidden when not checked) */
  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
  }

  /* Show the checkmark when checked */
  .containers input:checked ~ .checkmark:after {
    display: block;
  }

  /* Style the checkmark/indicator */
  .containers .checkmark:after {
    left: 9px;
    top: 5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
  }
  </style>
</head>
<body class="hold-transition login-page">
  <div class="limiter">
    <div class="container-login100">
      <div class="wrap-login100" style="padding-bottom: 30px">
        <!-- <div class="col-md-4 col-xs-12">
          <div class="login100-pic js-tilt" style="padding-top: 70px;width: 250px;">
            <img src="{{ url("images/logo_mirai.png")}}">
          </div>
        </div> -->

        <div class="col-md-12 col-xs-12 pl-md-5" style="margin-top: 20px;">
        <center><img src="{{url('logo_yamaha.png')}}" style="width:300px;padding-bottom: 0px;" > </center>
          <form method="post" action="{{ url('register/confirm') }}">

            {{ csrf_field() }}
             <span class="login100-form-title"  style="color: white;background-color: #605ca8;padding-bottom: 0;text-align: center;font-size: 20px;font-weight: bold;padding: 10px;border-radius: 16px;margin-top: 20px;">
              <b>Create Account</b>
            </span>

            @if (session('error'))
                  <div class="alert alert-danger alert-dismissible">
                      <h4 style="font-size: 15px;font-weight: bold;"> Error!</h4>
                      <span style="font-size: 12px">{{ session('error') }}</span>
                  </div>   
              @endif
              @if (session('success'))
                  <div class="alert alert-success alert-dismissible">
                      <h4 style="font-size: 15px;font-weight: bold;"> Success!</h4>
                      <span style="font-size: 12px">{{ session('success') }}</span>
                  </div>   
              @endif


            <div class="wrap-input100 validate-input"  style="padding-top:10px">
              <input autocomplete="off" type="text" class="input100" placeholder="Company" id="company" name="company" required autofocus>
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-home" aria-hidden="true"></i>
              </span>
            </div>
            

            <div class="wrap-input100 validate-input">
              <input autocomplete="off" type="text" class="input100" placeholder="Full Name" id="full_name" name="full_name" required autofocus>
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-user" aria-hidden="true"></i>
              </span>
            </div>

            <div class="wrap-input100 validate-input" >
              <input autocomplete="off" type="text" class="input100" placeholder="Email" id="email" name="email" required autofocus>
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-envelope" aria-hidden="true"></i>
              </span>
            </div>

            <div class="wrap-input100 validate-input" >
              <input autocomplete="off" type="text" class="input100" placeholder="Username" id="username" name="username" required autofocus>
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-vcard" aria-hidden="true"></i>
              </span>
            </div>

            <div class="wrap-input100 validate-input" >
              <input autocomplete="off" type="password" class="input100" placeholder="Password" id="password" name="password" required autofocus>
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
              </span>
            </div>

            <div class="wrap-input100 validate-input" >
              <input autocomplete="off" type="password" class="input100" placeholder="Confirm Password" id="password_confirm" name="password_confirm" required autofocus>
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
              </span>
            </div>

            <div>
                <label class="containers" onclick="register()"> Dengan menekan REGISTER, Anda menyetujui <a target="_blank" href="{{url('terms')}}">Syarat dan Ketentuan</a> serta <a target="_blank" href="{{url('policy')}}">Kebijakan dan Privasi</a> kami.
                  <input type="checkbox" id="reg" name="reg">
                  <span class="checkmark"></span>
                </label>
              </div>


            <div class="container-login100-form-btn">
              <button class="login100-form-btn" type="submit" id="btn-register">
                Register
              </button>
            </div>

            <div class="text-center p-t-12">
              <a class="txt2" href="{{url('')}}">
                <i class="fa fa-long-arrow-left m-l-5" aria-hidden="true"></i>
                <b style="font-size:16px">Back To Login</b>
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

    jQuery(document).ready(function() {
      $('#password_confirm').val('');
      $('#password').val('');

      $('#btn-register').attr('disabled', false);
      $('#btn-register').css('background-color','#000');

    });


    function register() {
      var returns = '';
      $("input[name='reg']:checked").each(function (i) {
        returns = $(this).val();
      });

      if (returns == 'on') {  
        $('#btn-register').attr('disabled', false);
        $('#btn-register').css('background-color','');
      }
      else{
        $('#btn-register').attr('disabled', true);
        $('#btn-register').css('background-color','#000');
      }
    }
  </script>
</body>
</html>