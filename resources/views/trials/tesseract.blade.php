@extends('layouts.display')
@section('stylesheets')
<!-- v2 -->
<script src='https://unpkg.com/tesseract.js@v2.1.0/dist/tesseract.min.js'></script>

<!-- v1 -->
<script src='https://unpkg.com/tesseract.js@1.0.19/src/index.js'></script>
<style type="text/css">
  .content-wrapper {
    background-color: #c430d0 !important;
  }

  .skin-purple .main-header .navbar {
    background-color: #c430d0 !important;
  }

  .navbar-header {
    visibility: hidden;
  }

  .navbar-custom-menu {
    visibility: hidden;
  }

  @font-face {
    font-family: GentyDemo;
    src: url("{{ url("fonts/GentyDemo-Regular.ttf")}}");
  }

  h2 {
    font-family: GentyDemo, sans-serif;
    color: white; 
    text-align: center;
    font-size: 65px;
  }

  h1 {
    font-family: GentyDemo, sans-serif;
    color: yellow; 
    text-align: center;
    font-size: 65px;
  }
</style>
@endsection
@section('header')

@endsection
@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <h2>Happy Birthday</h2>
      <h1>tes</h1>
    </div>
  </section>
  @stop

  @section('scripts')
  <script src="{{ url("js/jszip.min.js")}}"></script>
  <script src="{{ url("js/vfs_fonts.js")}}"></script>
  <script>
    jQuery(document).ready(function() {
      

      const worker = createWorker();

      (async () => {
        await worker.load();
        await worker.loadLanguage('eng+chi_tra');
        await worker.initialize('eng+chi_tra');
        const { data: { text } } = await worker.recognize('https://tesseract.projectnaptha.com/img/eng_bw.png');
        console.log(text);
        await worker.terminate();
      })();
    })
  </script>

  @stop