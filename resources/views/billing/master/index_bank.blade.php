@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<!-- <link href="{{ url("css/bootstrap.css") }}" rel="stylesheet"> -->
<link href="{{ url("css/dataTables.bootstrap4.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">

<style type="text/css">
    thead>tr>th {
        text-align:center;
        overflow:hidden;
        color: white !important;
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
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="page-title mb-0 p-0">{{$title}}<span class="text-purple"> {{$title_jp}}</span></h3>
            </div>
            <!-- <div class="col-md-6 col-8 align-self-right" style="text-align: right;">
                <a href="{{ url("/index/upload_invoice") }}" class="btn btn-primary"><i class="fa fa-upload"></i> <span class="hide-menu">Upload Invoice</span></a>
            </div> -->
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
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table user-table no-wrap" id="TableBank">
                                <thead>
                                    <tr>
                                        <th style="background-color: #757ce8;">Vendor Name</th>
                                        <th style="background-color: #757ce8;">Currency</th>
                                        <th style="background-color: #757ce8;">Bank & Branch Name</th>
                                        <th style="background-color: #757ce8;">Nama Rekening</th>
                                        <th style="background-color: #757ce8;">No Rekening</th>
                                        <th style="background-color: #757ce8;">Internal</th>
                                        <th style="background-color: #757ce8;">LN</th>
                                        <th style="background-color: #757ce8;">Not Active</th>
                                        <th style="background-color: #757ce8;">Vendor Address</th>
                                        <th style="background-color: #757ce8;">City</th>
                                        <th style="background-color: #757ce8;">Country</th>
                                        <th style="background-color: #757ce8;">Bank Address</th>
                                        <th style="background-color: #757ce8;">Bank Name</th>
                                        <th style="background-color: #757ce8;">Bank Branch</th>
                                        <th style="background-color: #757ce8;">Bank City Country</th>
                                        <th style="background-color: #757ce8;">Switch Code</th>
                                        <th style="background-color: #757ce8;">Full Amount</th>
                                        <th style="background-color: #757ce8;">Sett Acc No</th>
                                        <th style="background-color: #757ce8;">Sector Select</th>
                                        <th style="background-color: #757ce8;">Resident</th>
                                        <th style="background-color: #757ce8;">Citizenship</th>
                                        <th style="background-color: #757ce8;">Relation</th>
                                        <th style="background-color: #757ce8;">Bank Charge</th>
                                        <th style="background-color: #757ce8;">Action</th>
                                    </tr>
                                </thead>
                               <tbody id="bodyTableBank">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalcheck" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="modalcheckhide()">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    Apakah anda sudah mengecek tanda terima ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="modalcheckhide()">Cancel</button>
                    <a id="a" name="modalButtonCheck" type="button"  onclick="checked(this.id)" class="btn btn-success">Check</a>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalreject" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" onclick="modalrejecthide()">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    Apakah anda yakin ingin menolak tanda terima Ini ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="modalrejecthide()">Cancel</button>
                    <a id="a" name="modalButtonReject" type="button"  onclick="deletePR(this.id)" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

@stop
@section('scripts')

<script src="{{ url("js/jquery-3.5.1.js") }}"></script>
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
    });

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function getFormattedDate(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        
        return day + '-' + monthNames[month] + '-' + year;
    }

    function fetchData() {
        $.get('{{ url("fetch/bank") }}',  function(result, status, xhr){
            if(result.status){
                $('#TableBank').DataTable().clear();
                $('#TableBank').DataTable().destroy();
                $("#bodyTableBank").html('');
                var bodyTable = '';
                for (var i = 0; i < result.bank.length; i++) {
                    bodyTable += '<tr>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].vendor+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].currency+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].branch+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].rekening_no+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].rekening_nama+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].internal+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].ln+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].not_active+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].address_vendor+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].city+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].country+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].bank_address+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].bank_name+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].bank_branch+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].bank_city_country+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].switch_code+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].full_amount+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].settle_acc_no+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].sector_select+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].resident+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].citizenship+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].relation+'</td>';
                    bodyTable += '<td style="text-align:left">'+result.bank[i].bank_charge+'</td>';
                    bodyTable += '<td></td>';
                    bodyTable += '</tr>';
                }
                $('#bodyTableBank').append(bodyTable);

                var table = $('#TableBank').DataTable({
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

    function check(id) {
        $('#modalcheck').modal('show');
        $('[name=modalButtonCheck]').attr("id","check_"+id);
    }

    function modalcheckhide(){
        $('#modalcheck').modal('hide');
    }

    function rejectConfirmation(id) {
        $('#modalreject').modal('show');
        $('[name=modalButtonReject]').attr("id","reject_"+id);
    }

    function modalrejecthide(){
        $('#modalreject').modal('hide');
    }

    function checked(id){
        var id_check = id.split("_");

        var data = {
            id:id_check[1]
        }

        $("#loading").show();


        $.post('{{ url("checked/invoice") }}', data, function(result, status, xhr){
            if (result.status == true) {
                openSuccessGritter("Success","Data Berhasil Dihapus");
                $("#loading").hide();
                modalcheckhide();
                fetchData();
            }
            else{
                openErrorGritter("Success","Data Gagal Dihapus");
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