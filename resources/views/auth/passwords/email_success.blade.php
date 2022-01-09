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
  <link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">

</head>
<body class="hold-transition login-page">
  <div class="limiter">
    <div class="container-login100">
      <div class="wrap-login100">
        <div class="login100-pic js-tilt" data-tilt>
          <img src="{{ url("images/logo_mirai.png")}}">
        </div>

          <form method="post" action="{{ url('request/reset/password') }}">

          {{ csrf_field() }}
          <center>
            <span class="login100-form-title">
              <b>Request Reset Password Link</b>
            </span>

            <div class="wrap-input100 validate-input">
              <p><b>Your password reset request has been successful. <br>Please check your email. </b></p>
            </div>
          </center>

          <div class="container-login100-form-btn">
            <!-- <button class="login100-form-btn" type="submit">
              Send Email
            </button> -->
          </div>

          <div class="text-center p-t-12">
            <a class="txt2" href="{{url('')}}">
              <i class="fa fa-long-arrow-left m-l-5" aria-hidden="true"></i>
              Back To Login
            </a>
          </div>

          <div class="text-center p-t-136">
            <!-- <a class="txt2" href="{{url('')}}">
            <i class="fa fa-long-arrow-left m-l-5" aria-hidden="true"></i>
              Cancel
            </a> -->
          </div>
        </form>
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