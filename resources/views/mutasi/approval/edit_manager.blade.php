
@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Pengajuan Form Mutasi <span class="text-purple">突然変異フォームの提出</span>
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
@endsection
@section('content')
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <div class="box box-primary">

    <div class="box-header with-border">
    </div>  
    <form class="form-horizontal" role="form" method="post" action="{{url('edit/mutasi/approval/manager', $mutasi->id)}}">
            {{ csrf_field() }}
          <div class="box-body">
            <div class="col-md-12 col-md-offset-3">
              <div class="col-md-6">
                <div class="form-group">
                  <div class="col-sm-10">
                    <select class="form-control select2" id="apv_manager" name="apv_manager" data-placeholder='{{$mutasi->apv_atasan}}' style="width: 100%" required="">
                      <option value="">&nbsp;</option>
                      <option value="1">Setuju</option>
                      <option value="2">Tidak Setuju</option>
                    </select>
                  </div>
                  <div class="form-group pull-right">
                  <br>
                  <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </div>
            </div>
          </div>
        </form>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  $(function () {
    $('.select2').select2()
  })
</script>
@stop

