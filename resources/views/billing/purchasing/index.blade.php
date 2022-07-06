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
                <div class="card">
                    <div class="card-body" style="padding: 10px">
                        <h4 class="card-title"><span style="font-size: 20px; color: purple;"><i class="fa fa-angle-double-down"></i> Master & Report <i class="fa fa-angle-double-down"></i></span></h4>
                        <div class="text-end">
                            <a href="{{ url('index/vendor/registration') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;background-color: white;color: #094a05">Vendor Registration Data</a>
                            <!-- <a href="{{ url('') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;background-color: white;color: #094a05">All Invoice Data</a> -->
                            <a href="{{ url('index/vendor') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: purple;background-color: white;color: #094a05">Vendor Data</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-xs-6" style="text-align: center; padding: 5px">
                <div class="card">
                    <div class="card-body" style="padding: 10px">
                        <h4 class="card-title"><span style="font-size: 20px; color: #0e691e;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span></h4>
                        <div class="text-end">
                            <a href="{{ url('index/invoice') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: #0e691e;background-color: white;color: #094a05">Check Invoice</a>
                            <a href="{{ url('index/payment_request') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: #0e691e;background-color: white;color: #094a05">Payment Request</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-4 col-xs-6" style="text-align: center;padding: 5px">
                <div class="card">
                    <div class="card-body" style="padding: 10px">
                        <h4 class="card-title"><span style="font-size: 20px;color: #e61010"><i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i></span></h4>
                        <div class="text-end">
                            <a href="{{ url('index/payment_request/monitoring') }}" class="btn btn-default btn-block" style="font-size: 17px; border-color: #e61010;background-color: #fff;color: #4a0505 ">Monitoring Payment Request</a>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
@stop
@section('scripts')
@endsection