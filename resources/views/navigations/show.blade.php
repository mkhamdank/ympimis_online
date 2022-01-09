@extends('layouts.master')
@section('header')
<section class="content-header" style="padding:20px">
  <h1>
    Detail {{ $page }}
    <small></small>
  </h1>
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
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="box-header with-border">
      {{-- <h3 class="box-title">Detail User</h3> --}}
    </div>  
    <form role="form">
      <div class="box-body">
        <div class="form-group row">
          <label class="col-sm-3">Navigation Code</label>
          <div class="col-sm-5" align="left">
            {{$navigation->navigation_code}}
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">Navigation Name</label>
          <div class="col-sm-5" align="left">
            {{$navigation->navigation_name}}
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">Created By</label>
          <div class="col-sm-5" align="left">
            {{$navigation->user->name}}
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">Last Update</label>
          <div class="col-sm-5" align="left">
            {{$navigation->updated_at}}
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-3">Created At</label>
          <div class="col-sm-5" align="left">
            {{$navigation->created_at}}
          </div>
        </div>
      </form>
    </div>
    
  </div>

  @endsection

