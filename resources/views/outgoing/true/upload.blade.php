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
    #loading{
        display: none;
    }

    .datepicker-days > table > thead >tr>th,
    .datepicker-months > table > thead>tr>th,
    .datepicker-years > table > thead>tr>th,
    .datepicker-decades > table > thead>tr>th,
    .datepicker-centuries > table > thead>tr>th{
        background-color: white !important;
        color: #696969 !important;
    }
</style>
@stop
@section('header')
    <div class="page-breadcrumb" style="padding: 7px;padding-left: 15px">
        <div class="row align-items-center">
            <div class="col-md-12 col-12 align-self-center">
                <h3 class="page-title mb-0 p-0">{{$title}}<span class="text-purple"> {{$title_jp}}</span>
                    <button class="btn btn-info pull-right" style="margin-left: 5px; width: 15%;color: white" onclick="modalUploadSerialNumber();"><i class="fa fa-upload"></i> Upload Schedule</button>
                </h3>
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
                        <div class="col-md-12" style="padding: 10px">
                            <table class="table user-table no-wrap" id="tableUpload">
                                <thead>
                                    <tr>
                                        <th style="background-color: #3f50b5;color: white !important;color: black">Periode</th>
                                        <th style="background-color: #3f50b5;color: white !important;color: black">Serial Number</th>
                                        <th style="background-color: #3f50b5;color: white !important;color: black">Material</th>
                                        <th style="background-color: #3f50b5;color: white !important;color: black">Quantity</th>
                                        <th style="background-color: #3f50b5;color: white !important;color: black">Quantity Actual</th>
                                        <th style="background-color: #3f50b5;color: white !important;color: black">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTableUpload">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUploadSerialNumber" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header" style="margin-bottom: 20px;background-color: #f39c12;text-align: center;">
                    <center style="padding: 5px;width: 100%"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;color: white">UPLOAD MONTHLY SCHEDULE</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 pull-right">
                            <a class="btn btn-primary" style="width: 100%;margin-bottom: 5px;font-size: 12px;" href="{{url('download/serial_number/true')}}">Example</a>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-4" align="right">
                            <label for="" class="control-label">Periode<span class="text-red"> :</span></label>
                        </div>
                        <div class="col-md-8" align="left">
                            <input type="text" name="periode" id="periode" class="form-control datepicker" readonly placeholder="Pilih Periode">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-4" align="right">
                            <label for="" class="control-label">Upload File Excel<span class="text-red"> :</span></label>
                        </div>
                        <div class="col-md-8" align="left">
                            <input type="file" name="serialNumberFile" class="form-control" id="serialNumberFile">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: 10px;">
                    <div class="row" style="width: 100%">
                        <div class="col-md-6" align="left">
                            <button type="button" style="width: 100%;margin-bottom: 5px;font-size: 12px;color: white" class="btn btn-danger" onclick="$('#modalUploadSerialNumber').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
                        </div>
                        <div class="col-md-6" align="right">
                            <button onclick="uploadSerialNumber()" style="width: 100%;margin-bottom: 5px;font-size: 12px;color: white" class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditSerialNumber" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="margin-bottom: 20px;background-color: #f39c12;text-align: center;">
                    <center style="padding: 5px;width: 100%"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;color: white">EDIT MONTHLY SCHEDULE</h4></center>
                </div>
                <div class="modal-body">
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-3" align="right">
                            <label for="" class="control-label">Periode<span class="text-red"> :</span></label>
                        </div>
                        <div class="col-md-7" align="left">
                            <input type="text" name="edit_periode" id="edit_periode" class="form-control" readonly placeholder="Periode">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-3" align="right">
                            <label for="" class="control-label">Serial Number<span class="text-red"> :</span></label>
                        </div>
                        <div class="col-md-7" align="left">
                            <input type="text" name="edit_serial_number" class="form-control" id="edit_serial_number" placeholder="Serial Number" readonly>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-3" align="right">
                            <label for="" class="control-label">Material<span class="text-red"> :</span></label>
                        </div>
                        <div class="col-md-7" align="left">
                            <select class="form-control select2" id="edit_material" name="edit_material" style="width: 100%" data-placeholder="Pilih Material">
                                <option value=""></option>
                                @foreach($material as $material)
                                <option value="{{$material->material_number}}_{{$material->material_description}}">{{$material->material_number}} - {{$material->material_description}}</option>
                                @endforeach
                            </select>
                            <!-- <input type="text" name="edit_material" class="form-control" id="edit_material" placeholder="Material" readonly> -->
                        </div>
                    </div>

                    <div class="row" style="margin-top: 20px">
                        <div class="col-md-3" align="right">
                            <label for="" class="control-label">Quantity<span class="text-red"> :</span></label>
                        </div>
                        <div class="col-md-7" align="left">
                            <input type="text" name="edit_qty" class="form-control" id="edit_qty" placeholder="Quantity">
                            <input type="hidden" name="edit_id" class="form-control" id="edit_id">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: 10px;">
                    <div class="row" style="width: 100%">
                        <div class="col-md-6" align="left">
                            <button type="button" style="width: 100%;margin-bottom: 5px;font-size: 12px;color: white" class="btn btn-danger" onclick="$('#modalEditSerialNumber').modal('hide')"><i class="fa fa-close"></i> Cancel</button>
                        </div>
                        <div class="col-md-6" align="right">
                            <button onclick="updateSerialNumber()" style="width: 100%;margin-bottom: 5px;font-size: 12px;color: white" class="btn btn-success"><i class="fa fa-edit"></i> Update</button>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    jQuery(document).ready(function() {
        fetchData();
        $('.select2').select2({
            dropdownParent:$('#modalEditSerialNumber')
        });
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months",
            autoclose: true,
        });
    });

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function fetchData() {
        $.get('{{ url("fetch/serial_number/true") }}',  function(result, status, xhr){
            if(result.status){
                $('#tableUpload').DataTable().clear();
                $('#tableUpload').DataTable().destroy();
                $("#bodyTableUpload").html('');
                var bodyTable = '';
                for (var i = 0; i < result.serial_number.length; i++) {
                    bodyTable += '<tr>';
                    bodyTable += '<td>'+result.serial_number[i].periode+'<input type="hidden" value="'+result.serial_number[i].date+'" id="due_date"></td>';
                    bodyTable += '<td>'+result.serial_number[i].serial_number+'</td>';
                    bodyTable += '<td>'+result.serial_number[i].material_number+' - '+result.serial_number[i].part_name+'</td>';
                    bodyTable += '<td>'+result.serial_number[i].qty+'</td>';
                    bodyTable += '<td>'+result.serial_number[i].qty_actual+'</td>';
                    bodyTable += '<td><button class="btn btn-warning btn-sm" onclick="editSerialNumber(\''+result.serial_number[i].id+'\',\''+result.serial_number[i].date+'\',\''+result.serial_number[i].serial_number+'\',\''+result.serial_number[i].material_number+'\',\''+result.serial_number[i].part_name+'\',\''+result.serial_number[i].qty+'\')">Edit</button><button style="margin-left:5px;color:white" class="btn btn-danger btn-sm" onclick="deleteSerialNumber(\''+result.serial_number[i].id+'\')">Delete</button></td>';
                    bodyTable += '</tr>';
                }
                $('#bodyTableUpload').append(bodyTable);

                var table = $('#tableUpload').DataTable({
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

    function editSerialNumber(id,date,serial_number,material_number,part_name,qty) {
        $('#modalEditSerialNumber').modal('show');
        $('#edit_periode').val(date);
        $('#edit_serial_number').val(serial_number);
        $('#edit_material').val(material_number +'_'+part_name).trigger('change');
        $('#edit_qty').val(qty);
        $('#edit_id').val(id);
    }

    function modalUploadSerialNumber() {
        $('#modalUploadSerialNumber').modal('show');
        $('#periode').val('');
    }

    function uploadSerialNumber() {
        $('#loading').show();
        var periode = $('#periode').val();
        if($('#serialNumberFile').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '' || periode == ''){
            openErrorGritter('Error!', 'Pilih File Excel & Periode');
            audio_error.play();
            $('#loading').hide();
            return false;   
        }

        var formData = new FormData();
        var newAttachment  = $('#serialNumberFile').prop('files')[0];
        var file = $('#serialNumberFile').val().replace(/C:\\fakepath\\/i, '').split(".");

        formData.append('newAttachment', newAttachment);

        formData.append('extension', file[1]);
        formData.append('file_name', file[0]);
        formData.append('periode', periode);

        $.ajax({
            url:"{{ url('upload/serial_number/true') }}",
            method:"POST",
            data:formData,
            dataType:'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success:function(data)
            {
                if (data.status) {
                    openSuccessGritter('Success!',data.message);
                    $('#serialNumberFile').val("");
                    $('#modalUploadSerialNumber').modal('hide');
                    $('#loading').hide();
                    fetchData();
                }else{
                    openErrorGritter('Error!',data.message);
                    audio_error.play();
                    $('#loading').hide();
                }

            }
        });
    }

    function updateSerialNumber() {
        $('#loading').show();
        var id = $('#edit_id').val();
        var qty = $('#edit_qty').val();
        var material = $('#edit_material').val();

        var data = {
            id:id,
            qty:qty,
            material:material,
        }
        $.get('{{ url("update/serial_number/true") }}',data,  function(result, status, xhr){
            if(result.status){
                openSuccessGritter('Success!',result.message);
                $('#modalEditSerialNumber').modal('hide');
                $('#loading').hide();
                fetchData();
            }else{
                openErrorGritter('Error!',result.message);
                audio_error.play();
                $('#loading').hide();
            }
        });
    }

    function deleteSerialNumber(id) {
        if (confirm('Apakah Anda yakin akan menghapus data?')) {
            $('#loading').show();

            var data = {
                id:id,
            }
            $.get('{{ url("delete/serial_number/true") }}',data,  function(result, status, xhr){
                if(result.status){
                    openSuccessGritter('Success!',result.message);
                    $('#loading').hide();
                    fetchData();
                }else{
                    openErrorGritter('Error!',result.message);
                    audio_error.play();
                    $('#loading').hide();
                }
            });
        }
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