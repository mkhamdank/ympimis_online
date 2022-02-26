@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<!-- <link href="{{ url("css/bootstrap.css") }}" rel="stylesheet"> -->
<link href="{{ url("css/dataTables.bootstrap4.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">
<style type="text/css">
    thead>tr>th{
        text-align:center;
        overflow:hidden;
    }
    tbody>tr>td{
        text-align:left;
    }
    tfoot>tr>th{
        text-align:center;
    }
    th:hover {
        overflow: visible;
    }
    td:hover {
        overflow: visible;
    }
    table.table-bordered{
        border:1px solid grey;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid grey;
        padding-top: 0;
        padding-bottom: 0;
        vertical-align: middle;
    }
    table.table-bordered > tbody > tr > td{
        padding: 0px;
        vertical-align: middle;
    }
    table.table-bordered > tfoot > tr > th{
        padding:0;
        vertical-align: middle;
        color: #fff !important;
    }
    thead {
        background-color: #fff;
        color: #fff;
    }
    td{
        overflow:hidden;
        text-overflow: ellipsis;
    }
    th{
        color: white;
    }
</style>
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
<meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid" style="padding: 7px;min-height: 100vh">
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        <div class="row" style="padding: 5px">
            <div class="col-sm-12 col-xs-12" style="text-align: center;">
                <div class="card">
                    <div class="card-body" style="padding: 0px">
                        <div class="col-md-12" style="padding: 10px;overflow-x: scroll;">
                            <table class="table user-table no-wrap" id="tableKensa">
                                <thead>
                                    <tr>
                                        <th style="background-color: #3f50b5;color: white !important;">Serial Number</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Material</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Inspector</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Qty Check</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Total OK</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Total NG</th>
                                        <th style="background-color: #3f50b5;color: white !important;">NG Ratio (%)</th>
                                        <th style="background-color: #3f50b5;color: white !important;">NG Name</th>
                                        <th style="background-color: #3f50b5;color: white !important;">NG Qty</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Lot Status</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Recheck Status</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTableKensa">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
<script src="{{ url("js/jquery-3.5.1.js") }}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ url("js/dataTables.bootstrap4.min.js") }}"></script>
<script src="{{ url("js/jquery.flot.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        fetchData();
    });

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function fetchData() {
        $.get('{{ url("fetch/kensa/true/report") }}',  function(result, status, xhr){
            if(result.status){
                $('#tableKensa').DataTable().clear();
                $('#tableKensa').DataTable().destroy();
                $("#bodyTableKensa").html('');
                var bodyTable = '';
                for (var i = 0; i < result.outgoing.length; i++) {
                    var background = '';
                    if (result.outgoing[i].lot_status == 'LOT OUT') {
                        background = 'background-color:#ffc4c7';
                    }
                    bodyTable += '<tr style="'+background+'">';
                    bodyTable += '<td>'+result.outgoing[i].serial_number+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].material_number+'<br>'+result.outgoing[i].material_description+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].inspector+'</td>';
                    bodyTable += '<td style="text-align:right">'+result.outgoing[i].qty_check+'</td>';
                    bodyTable += '<td style="text-align:right">'+result.outgoing[i].total_ok+'</td>';
                    bodyTable += '<td style="text-align:right">'+result.outgoing[i].total_ng+'</td>';
                    bodyTable += '<td style="text-align:right">'+result.outgoing[i].ng_ratio+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].ng_name+'</td>';
                    bodyTable += '<td style="text-align:right">'+result.outgoing[i].ng_qty+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].lot_status+'</td>';
                    if (result.outgoing[i].lot_status == 'LOT OUT' && result.outgoing[i].recheck_status == 'Checked') {
                        bodyTable += '<td style="background-color:#dcffc4">'+(result.outgoing[i].recheck_status || '')+'</td>';
                    }else if(result.outgoing[i].lot_status == 'LOT OUT' && result.outgoing[i].recheck_status == null){
                        var url = '{{url("index/outgoing/true/input/lot_out/")}}'+'/'+result.outgoing[i].serial_number+'/'+result.outgoing[i].check_date;
                        bodyTable += '<td><a class="btn btn-primary btn-sm" href="'+url+'">Recheck</a></td>';
                    }else{
                        bodyTable += '<td>'+(result.outgoing[i].recheck_status || '')+'</td>';
                    }
                    bodyTable += '</tr>';
                }
                $('#bodyTableKensa').append(bodyTable);

                var table = $('#tableKensa').DataTable({
                    'dom': 'Bfrtip',
                    'responsive':true,
                    'lengthMenu': [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                    ],
                    'buttons': {
                        buttons:[
                        {
                            extend: 'pageLength',
                            className: 'btn btn-default',
                        },
                        {
                            extend: 'copy',
                            className: 'btn btn-success',
                            text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-info',
                            text: '<i class="fa fa-file-excel-o"></i> Excel',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-warning',
                            text: '<i class="fa fa-print"></i> Print',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        }
                        ]
                    },
                    'paging': true,
                    'lengthChange': true,
                    'pageLength': 10,
                    'searching': true   ,
                    'ordering': true,
                    'order': [],
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true
                });
                $('#loading').hide();
            }else{
                openErrorGritter('Error!','Failed Get Data');
            }
        });
    }

    function openSuccessGritter(title, message){
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-success',
            image: '{{ url("images/image-screen.png") }}',
            sticky: false,
            time: '3000'
        });
    }

    function openErrorGritter(title, message) {
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-danger',
            image: '{{ url("images/image-stop.png") }}',
            sticky: false,
            time: '3000'
        });
    }
</script>
@endsection