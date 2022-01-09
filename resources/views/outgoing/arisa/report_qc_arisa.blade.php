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
        border:1px solid black;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        padding-top: 0;
        padding-bottom: 0;
        vertical-align: middle;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid black;
        padding: 0px;
        vertical-align: middle;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid black;
        padding:0;
        vertical-align: middle;
        background-color: rgb(126,86,134);
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
            <div class="col-md-12 col-8 align-self-center">
                <h3 class="page-title mb-0 p-0">{{$title}}<span class="text-purple"> {{$title_jp}}</span><button style="margin-right:2px;color: white" class="btn btn-sm btn-primary pull-right" onclick="$('#modalSoNumber').modal('show')">Input SO Number</button></h3>
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
                            <table class="table table-bordered user-table no-wrap" id="tableKensa">
                                <thead>
                                    <tr>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">SO Number</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Final Serial Number</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Result Prod. Check</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 2%">Material</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Inspector</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Point Check Type</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Qty Check</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 3%">Point Check</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 2%">Standard</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Upper</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Lower</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Product No.</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Result</th>
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

<div class="modal fade" id="modalSoNumber" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header" style="background-color: #ffc654;text-align: center;">
                
            </div> -->
            <div class="modal-body">

                <div style="background-color: #ffc654;text-align: center;margin-bottom: 10px">
                    <center style="padding: 5px;width: 100%"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;">PILIH HASIL QC FINAL CEK</h4></center>
                </div>
                <div class="row" id="divSerialNumber" style="margin-bottom: 10px">
                    <div class="col-md-12">
                        <select class="form-control" id="final_serial_number" name="final_serial_number" data-placeholder="Pilih Serial Number" style="width: 100%">

                        </select>
                    </div>
                </div>

                <div style="background-color: #ffc654;text-align: center;margin-bottom: 10px">
                    <div class="col-md-12">
                        <center style="padding: 5px;width: 100%"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;">MASUKKAN SALES ORDER NUMBER</h4></center>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="pull-right" name="so_number" style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="so_number" placeholder="Sales Order Number">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12" style="margin:0px">
                    <div class="row">
                        <button class="btn btn-success" style="width: 100%;margin-bottom: 5px;font-size: 20px;margin-top: 10px;padding-right: 0px" id="btn_product_fix" onclick="confirmSoNumber()">CONFIRM</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('scripts')
<!-- <script src="{{ url("js/jquery-3.5.1.js") }}"></script> -->
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery(document).ready(function() {
        fetchData();
        $('#final_serial_number').select2({
            dropdownParent: $("#divSerialNumber"),
            allowClear:true
        });
    });

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function fetchData() {
        $.get('{{ url("fetch/outgoing/arisa/report") }}',  function(result, status, xhr){
            if(result.status){
                $('#tableKensa').DataTable().clear();
                $('#tableKensa').DataTable().destroy();
                $("#bodyTableKensa").html('');
                var bodyTable = '';
                var final_serial_number = [];
                var allcheck = uniqByKeepFirst(result.allchecks, it => it.serial_number);
                for (var i = 0; i < result.outgoing.length; i++) {
                    bodyTable += '<tr>';
                    bodyTable += '<td>'+(result.outgoing[i].so_number || '')+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].final_serial_number+'</td>';
                    var sernum = result.outgoing[i].serial_number.split(',');
                    bodyTable += '<td>';
                    for(var j = 0; j < sernum.length;j++){
                        for(var k = 0; k < allcheck.length;k++){
                            if (sernum[j] == allcheck[k].serial_number) {
                                bodyTable += sernum[j]+'<br>OK : '+allcheck[k].result_check.split('_')[2]+'<br>NG : '+allcheck[k].result_check.split('_')[3]+'<hr style="border:2px solid red;padding:0px;margin-top:0px;margin-bottom:0px">';
                            }
                        }
                    }
                    bodyTable += '</td>';
                    bodyTable += '<td>'+result.outgoing[i].material_number+'<br>'+result.outgoing[i].material_description+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].inspector+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].point_check_type+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].qty_check+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].point_check_name+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].point_check_standard+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].point_check_upper+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].point_check_lower+'</td>';
                    bodyTable += '<td>'+result.outgoing[i].point_check_index+'</td>';
                    if (result.outgoing[i].product_result == 'OK') {
                        var color = '#91ff78';
                    }else if(result.outgoing[i].product_result == 'NG'){
                        var color = '#ff9191';
                    }else{
                        var color = 'none';
                    }
                    bodyTable += '<td style="background-color:'+color+'">'+result.outgoing[i].product_result+'</td>';
                    bodyTable += '</tr>';
                    if (result.outgoing[i].so_number == null) {
                        final_serial_number.push(result.outgoing[i].final_serial_number);
                    }
                }
                $('#bodyTableKensa').append(bodyTable);

                var final_serial_numbers = final_serial_number.filter(onlyUnique);
                $('#final_serial_number').html('');
                var final_select = '';
                final_select += '<option value=""></option>';
                for(var l = 0; l < final_serial_numbers.length;l++){
                    final_select += '<option value="'+final_serial_numbers[l]+'">'+final_serial_numbers[l]+'</option>';
                }

                $('#final_serial_number').append(final_select);

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

    function onlyUnique(value, index, self) {
      return self.indexOf(value) === index;
    }

    function dynamicSort(property) {
        var sortOrder = 1;
        if(property[0] === "-") {
            sortOrder = -1;
            property = property.substr(1);
        }
        return function (a,b) {
            var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
            return result * sortOrder;
        }
    }

    function confirmSoNumber() {
        $('#loading').show();
        var final_serial_number = $('#final_serial_number').val();
        var so_number = $('#so_number').val();

        var data = {
            final_serial_number:final_serial_number,
            so_number:so_number,
        }

        $.get('{{ url("input/outgoing/arisa/so_number") }}',data,  function(result, status, xhr){
            if(result.status){
                fetchData();
                $('#final_serial_number').val('').trigger('change');
                $('#so_number').val('');
                $('#loading').hide();
                $('#modalSoNumber').modal('hide');
                openSuccessGritter('Success!','Success Input SO Number');
            }else{
                $('#loading').hide();
                openErrorGritter('Error!',result.message);
            }
        });
    }

    function uniqByKeepFirst(a, key) {
        let seen = new Set();
        return a.filter(item => {
            let k = key(item);
            return seen.has(k) ? false : seen.add(k);
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