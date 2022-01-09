@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    thead>tr>th{
        text-align:center;
    }
    tbody>tr>td{
        text-align:center;
    }
    tfoot>tr>th{
        text-align:center;
    }
    td:hover {
        overflow: visible;
    }
    table.table-bordered{
        border:1px solid black;
        margin-bottom: 5px;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        margin:0;
        padding:0;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(180,180,180);
        font-size: 12px;
        background-color: rgb(240,240,240);
        padding-top: 2px;
        padding-bottom: 2px;
        padding-left: 3px;
        padding-right: 3px;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loading, #error { display: none; }
    .marquee {
        width: 100%;
        overflow: hidden;
        margin: 0px;
        padding: 0px;
        text-align: center;
        height: 35px;
    }
</style>
@stop
@section('header')
<section class="content-header" style="padding: 0; margin:0;">
    <div class="marquee">
        <span style="font-size: 16px;" class="text-purple"><span style="font-size:22px;"><b>M</b></span>anufactur<span style="font-size:23px;"><b>i</b></span>ng <span style="font-size:22px;"><b>R</b></span>ealtime <span style="font-size:22px;"><b>A</b></span>cquisition of <span style="font-size:22px;"><b>I</b></span>nformation</span>
        <br>
        <b><span style="font-size: 20px;" class="text-purple">
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="24px">
            製 造 の リ ア ル タ イ ム 情 報
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="24px">
        </span></b>
    </div>
</section>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Production Support<br/>生産支援モニ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Manpower Information (人工の情報)</span>
                            <br>
                            <a href="{{ url("index/report/manpower") }}">
                                <i class="fa fa-caret-right"></i> Manpower Information (人工の情報)
                            </a>
                            <br>
                            <a href="{{ url("index/report/total_meeting") }}">
                                <i class="fa fa-caret-right"></i> Total Meeting (トータルミーティング)
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Presence Information (出勤情報)</span>
                            <br>
                            <a href="{{ url("index/report/employee_resume") }}">
                                <i class="fa fa-caret-right"></i> Employee Resume (従業員のまとめ)
                            </a>
                            <br>
                            <a href="{{ url("index/report/absence") }}">
                                <i class="fa fa-caret-right"></i> Absence (欠勤)
                            </a>
                            <br>
                            <a href="{{ url("index/report/attendance_data")}}">
                                <i class="fa fa-caret-right"></i> Attendance Data (出席データ)
                            </a>
                            <br>
                            <a href="{{ url("index/report/checklog_data")}}">
                                <i class="fa fa-caret-right"></i> Checklog Data (出退勤登録データ)
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
@section('scripts')
@stop