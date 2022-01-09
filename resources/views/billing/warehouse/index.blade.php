@extends('layouts.master')
@section('stylesheets')
@stop
@section('header')
    <div class="page-breadcrumb" style="padding: 7px">
        <div class="row align-items-center">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="page-title mb-0 p-0">{{$title}}<span class="text-purple"> {{$title_jp}}</span></h3>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid" style="padding: 7px;min-height: 100vh">
        <div class="row"  style="padding: 5px">
            <div class="col-sm-4 col-xs-6" style="text-align: center; padding: 5px">
            </div>

            <div class="col-sm-4 col-xs-6" style="text-align: center; padding: 5px">
            </div>

            <div class="col-sm-4 col-xs-6" style="text-align: center;padding: 5px">
            </div>
        </div>
    </div>
@stop
@section('scripts')
@endsection