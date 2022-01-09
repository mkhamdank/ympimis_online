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
        text-align:center;
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

    .bootstrap-datetimepicker-widget { background-color: #fff !important; }

    .datepicker-days > table > thead >tr>th,
    .datepicker-months > table > thead>tr>th,
    .datepicker-years > table > thead>tr>th,
    .datepicker-decades > table > thead>tr>th,
    .datepicker-centuries > table > thead>tr>th{
        background-color: white;
        color: #999 !important;
    }
</style>
@stop
@section('header')
    <div class="page-breadcrumb" style="padding-top: 10px;padding-left: 15px;padding-bottom: 10px">
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
                        <h4 style="padding: 0px">Filter</h4>
                            <div class="col-md-4" style="padding-left: 5px;text-align: left;display: inline-block;">
                                <label>Date From</label>
                                <div class="input-group date">
                                    <div class="input-group-addon bg-green" style="border: none; background-color: #469937; color: white;">
                                        <i class="fa fa-calendar" style="padding: 10px"></i>
                                    </div>
                                    <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
                                </div>
                            </div>
                            <div class="col-md-4" style="padding-left: 5px;text-align: left;display: inline-block;">
                                <label>Date To</label>
                                <div class="input-group date">
                                    <div class="input-group-addon bg-green" style="border: none; background-color: #469937; color: white;">
                                        <i class="fa fa-calendar" style="padding: 10px"></i>
                                    </div>
                                    <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date To">
                                </div>
                            </div>
                            <div class="col-md-4" style="padding-left: 5px;text-align: left;display: inline-block;padding-top: 10px">
                                <label>Inspection Level</label>
                                <div class="form-group">
                                    <select class="form-control select2" multiple="multiple" id='inspectionLevelSelect' onchange="changeInspectionLevel()" data-placeholder="Select Inspection Level" style="width: 100%;color: black !important">
                                        @foreach($inspection_levels as $inspection_level)
                                        <option value="{{$inspection_level->inspection_level}}">{{$inspection_level->inspection_level}}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="inspection_level" id="inspection_level" style="color: black !important" hidden>
                                </div>
                            </div>
                            <div class="col-md-4" style="padding-left: 5px;text-align: left;display: inline-block;padding-top: 10px">
                                <label>Material</label>
                                <div class="form-group">
                                    <select class="form-control select2" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
                                        @foreach($materials as $material)
                                        <option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="material" id="material" style="color: black !important" hidden>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ url('index/outgoing/'.$vendor) }}" class="btn btn-warning">Back</a>
                                    <a href="{{ url('index/incoming/arisa/report') }}" class="btn btn-danger">Clear</a>
                                    <a class="btn btn-primary col-sm-14" href="javascript:void(0)" onclick="fetchData()">Search</a>
                                    <!-- <button class="btn btn-success"><i class="fa fa-download"></i> Export Excel Without Merge</button> -->
                                    <!-- <input class="btn btn-success" type="submit" name="publish" value="Export Excel Without Merge">
                                    <input class="btn btn-warning" type="submit" name="save" value="Export Excel With Merge"> -->
                                </div>
                            </div>
                        <div class="col-md-12" style="padding: 10px;overflow-x: scroll;">
                            <table class="table user-table no-wrap" id="tableReport">
                                <thead>
                                    <tr>
                                        <th style="background-color: #3f50b5;color: white !important;">Date</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Invoice</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Inspection Level</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Lot Number</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Material</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Desc</th>
                                        <th style="background-color: #3f50b5;color: white !important;">HPL</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Qty Rec</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Qty Check</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Defect</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Repair</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Return</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Scrap</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Note</th>
                                        <th style="background-color: #3f50b5;color: white !important;">NG Ratio</th>
                                        <th style="background-color: #3f50b5;color: white !important;">Lot Status</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTableReport">
                                    
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
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
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
        $('.select2').select2();
        fetchData();
        $('.datepicker').datepicker({
            <?php $tgl_max = date('Y-m-d') ?>
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,   
            endDate: '<?php echo $tgl_max ?>'
        });
    });

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function changeMaterial() {
        $("#material").val($("#materialSelect").val());
    }
    function changeInspectionLevel() {
        $("#inspection_level").val($("#inspectionLevelSelect").val());
    }

    function fetchData() {
        var data = {
            vendor:'{{$vendor_name}}',
            material:$('#material').val(),
            inspection_level:$('#inspection_level').val(),
        }
        $.get('{{ url("fetch/incoming/arisa/report") }}',data,  function(result, status, xhr){
            if(result.status){
                $('#tableReport').DataTable().clear();
                $('#tableReport').DataTable().destroy();
                $("#bodyTableReport").html('');
                var tableData = '';
                var index = 0;
                $.each(result.incoming, function(key, value) {
                    
                    var jumlah = 0;

                    if (value.ng_name != null) {
                        var ng_name = value.ng_name.split('_');
                        var ng_qty = value.ng_qty.split('_');
                        var status_ng = value.status_ng.split('_');
                        if (value.note_ng != null) {
                            var note_ng = value.note_ng.split('_');
                        }else{
                            var note_ng = "";
                        }
                        jumlah = ng_name.length;
                    }else{
                        jumlah = 1;
                    }

                    
                    for (var i = 0; i < jumlah; i++) {
                        tableData += '<tr>';
                        tableData += '<td>'+ value.created +'</td>';
                        tableData += '<td>'+ value.invoice +'</td>';
                        tableData += '<td>'+ value.inspection_level +'</td>';
                        tableData += '<td>'+ value.lot_number +'</td>';
                        tableData += '<td>'+ value.material_number +'</td>';
                        tableData += '<td>'+ value.material_description +'</td>';
                        tableData += '<td>'+ value.hpl +'</td>';
                        tableData += '<td>'+ value.qty_rec +'</td>';
                        tableData += '<td>'+ value.qty_check +'</td>';
                        if (value.ng_name != null) {
                            tableData += '<td>';
                            tableData += '<span class="label label-danger">'+ng_name[i]+'</span><br>';
                            tableData += '</td>';
                            if (status_ng[i] == 'Repair') {
                                tableData += '<td>';
                                tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
                                tableData += '</td>';
                                tableData += '<td>';
                                tableData += '</td>';
                                tableData += '<td>';
                                tableData += '</td>';
                            }else if (status_ng[i] == 'Return') {
                                tableData += '<td>';
                                tableData += '</td>';
                                tableData += '<td>';
                                tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
                                tableData += '</td>';
                                tableData += '<td>';
                                tableData += '</td>';
                            }else if (status_ng[i] == 'Scrap') {
                                tableData += '<td>';
                                tableData += '</td>';
                                tableData += '<td>';
                                tableData += '</td>';
                                tableData += '<td>';
                                tableData += '<span class="label label-danger">'+ng_qty[i]+'</span><br>';
                                tableData += '</td>';
                            }
                            if (note_ng.length > 0) {
                                tableData += '<td>';
                                tableData += '<span class="label label-danger">'+note_ng[i]+'</span><br>';
                                tableData += '</td>';
                            }else{
                                tableData += '<td>';
                                tableData += '</td>';
                            }
                        }else{
                            tableData += '<td>';
                            tableData += '</td>';
                            tableData += '<td>';
                            tableData += '</td>';
                            tableData += '<td>';
                            tableData += '</td>';
                            tableData += '<td>';
                            tableData += '</td>';
                            tableData += '<td>';
                            tableData += '</td>';
                        }
                        tableData += '<td style="vertical-align:middle">'+ value.ng_ratio.toFixed(2) +'</td>';
                        tableData += '<td style="vertical-align:middle">'+ value.status_lot +'</td>';
                        tableData += '</tr>';
                    }
                    index++;
                });
                $('#bodyTableReport').append(tableData);

                var table = $('#tableReport').DataTable({
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