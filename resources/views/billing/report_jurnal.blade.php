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
    .datepicker-days > table > thead >tr>th,
    .datepicker-months > table > thead>tr>th,
    .datepicker-years > table > thead>tr>th,
    .datepicker-decades > table > thead>tr>th,
    .datepicker-centuries > table > thead>tr>th{
        background-color: white;
        color: #999 !important;
    }
    .loading{
        display: none;
    }
</style>
@stop
@section('header')
    <!-- <div class="page-breadcrumb" style="padding: 7px"> </div> -->
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
                <div class="card" style="margin-bottom: 5px !important">
                    <div class="card-body" style="padding: 0px">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                        <form method="GET" action="{{ url("export/bank/list") }}">
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
                                    <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
                                </div>
                            </div>
                            <div class="col-md-8" style="padding-left: 5px;text-align: left;display: inline-block;padding-top: 10px">
                                <label>Currency</label>
                                <div class="form-group">
                                    <select class="form-control select2" multiple="multiple" id='currencySelect' onchange="changeCurrency()" data-placeholder="Select Currency" style="width: 100%;color: black !important">
                                        <option value="IDR">IDR</option>
                                        <option value="EUR">EUR</option>
                                        <option value="USD">USD</option>
                                        <option value="JPY">JPY</option>
                                    </select>
                                    <input type="text" name="currency" id="currency" style="color: black !important" hidden>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button class="btn btn-primary col-sm-14" type="button" onclick="fetchData()">Search</button>
                                    <button class="btn btn-success" type="submit"><i class="fa fa-download"></i> Export Excel</button>
                                    <!-- <input class="btn btn-success" type="submit" name="publish" value="Export Excel Without Merge">
                                    <input class="btn btn-warning" type="submit" name="save" value="Export Excel With Merge"> -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-xs-12" style="text-align: center;">
                <div class="card" style="margin-bottom: 5px !important">
                    <div class="card-body" style="padding: 0px">
                        <div class="col-md-12" style="padding: 10px;overflow-x: scroll;">
                            <table class="table user-table no-wrap" id="tableBank">
                                <thead>
                                    <tr>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Date Payment</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Vendor Name</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Bank Name</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 2%">Bank Beneficiary Name</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Bank Beneficiary No</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Currency</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Amount</th>
                                        <th style="background-color: #3f50b5;color: white !important;width: 1%">Remark</th>
                                    </tr>
                                </thead>
                                <tbody id="bodytableBank">
                                    
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

    function changeCurrency() {
        $("#currency").val($("#currencySelect").val());
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function fetchData() {
        $('#loading').show();
        var data = {
            currency:$('#currency').val(),
            date_from:$('#date_from').val(),
            date_to:$('#date_to').val(),
        }
        $.get('{{ url("fetch/list_bank") }}',data,  function(result, status, xhr){
            if(result.status){
                $('#tableBank').DataTable().clear();
                $('#tableBank').DataTable().destroy();
                $("#bodytableBank").html('');
                var bodyTable = '';
                for (var i = 0; i < result.jurnal.length; i++) {
                    bodyTable += '<tr>';
                    bodyTable += '<td>'+getFormattedDateTime(new Date(result.jurnal[i].jurnal_date))+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].supplier_code+' - '+result.jurnal[i].supplier_name+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].bank_branch+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].bank_beneficiary_name+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].bank_beneficiary_no+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].currency+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].amount+'</td>';
                    bodyTable += '<td>'+result.jurnal[i].remark+'</td>';
                    bodyTable += '</tr>';
                }
                $('#bodytableBank').append(bodyTable);

                var table = $('#tableBank').DataTable({
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
                openErrorGritter('Error!',result.message);
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

    function getFormattedDateTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = (date.getMonth()+1).toString();
        month = month.length > 1 ? month : '0' + month;

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        if (second < 10) {
            second = "0" + second;
        }
        
        return year+'-'+month+'-'+day;
    }
</script>
@endsection