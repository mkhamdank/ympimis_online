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
            <div class="col-md-6 col-8 align-self-right" style="text-align: right;">
                <a href="{{ url("/index/upload_invoice") }}" class="btn btn-primary"><i class="fa fa-upload"></i> <span class="hide-menu">Upload Invoice</span></a>
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
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table user-table no-wrap" id="TableInvoice">
                                <thead>
                                    <tr>
                                        <th style="background-color: #757ce8;">Tanggal</th>
                                        <th style="background-color: #757ce8;">Vendor</th>
                                        <!-- <th style="background-color: #757ce8;">PIC</th> -->
                                        <!-- <th style="background-color: #757ce8;">Kwitansi</th> -->
                                        <th style="background-color: #757ce8;">Invoice</th>
                                        <th style="background-color: #757ce8;">Surat Jalan</th>
                                        <th style="background-color: #757ce8;">Faktur Pajak</th>
                                        <th style="background-color: #757ce8;">Purchase Order</th>
                                        <!-- <th style="background-color: #757ce8;">Note</th> -->
                                        <!-- <th style="background-color: #757ce8;">Currency</th> -->
                                        <th style="background-color: #757ce8;">Amount</th>
                                        <th style="background-color: #757ce8;">File</th>
                                        <th style="background-color: #757ce8;">TT</th>
                                        <th style="background-color: #757ce8;">Status</th>
                                    </tr>
                                </thead>
                               <tbody id="bodyTableInvoice">
                                    
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

        // $("#main-wrapper").attr("data-sidebartype","mini-sidebar");
        // document.getElementById("main-wrapper").setAttribute('data-sidebartype','mini-sidebar');
        // document.getElementById("logo-yamaha").setAttribute('height','20px');
        // document.getElementById("logo-yamaha").style.setProperty('height', '20px', 'important');
        // document.getElementsByClassName('logo-icon')[0].style.setProperty('padding-left', '0px', 'important');
        // document.getElementsByClassName('hide-menu')[0].style.setProperty('display', 'none', 'important');
        // document.getElementsByClassName('sidebar-item active selected')[0].style.setProperty('width', '65px', 'important');
     
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
        $.get('{{ url("fetch/invoice") }}',  function(result, status, xhr){
            if(result.status){
                $('#TableInvoice').DataTable().clear();
                $('#TableInvoice').DataTable().destroy();
                $("#bodyTableInvoice").html('');
                var bodyTable = '';
                for (var i = 0; i < result.invoice.length; i++) {
                    bodyTable += '<tr>';
                    bodyTable += '<td>'+getFormattedDate(new Date(result.invoice[i].tanggal))+'</td>';
                    bodyTable += '<td>'+result.invoice[i].supplier_name+'</td>';
                    // bodyTable += '<td>'+result.invoice[i].pic+'</td>';
                    // bodyTable += '<td>'+result.invoice[i].kwitansi+'</td>';
                    bodyTable += '<td>'+result.invoice[i].tagihan+'</td>';
                    bodyTable += '<td>'+result.invoice[i].surat_jalan+'</td>';
                    bodyTable += '<td>'+result.invoice[i].faktur_pajak+'</td>';
                    bodyTable += '<td>'+result.invoice[i].purchase_order+'</td>';
                    // bodyTable += '<td>'+result.invoice[i].note+'</td>';
                    bodyTable += '<td>'+result.invoice[i].currency+' '+parseInt(result.invoice[i].amount).toLocaleString('de-DE')+'</td>';
                    bodyTable += '<td><a href="http://10.109.33.10/mirai_online/public/files/invoice/'+result.invoice[i].file+'" target="_blank" class="fa fa-paperclip"></a></td>';
                    bodyTable += '<td> <a href="../report/invoice/'+result.invoice[i].id+'" target="_blank" class="btn btn-danger btn-xs" style="margin-right:5px;color:white" data-toggle="tooltip" title="Report PDF"><i class="fa fa-file-pdf-o"></i></a></td>';

                    if ("{{ Auth::user()->role_code == 'E - Billing'}}") {

                        if (result.invoice[i].status == "Open") {
                            bodyTable += '<td><span class="label label-warning">Check By Purchasing</span></td>';
                        }
                        else if(result.invoice[i].status == "checked_pch"){
                            bodyTable += '<td><span class="label label-success"><i class="fa fa-check"></i> Verified By Purchasing</span></td>';
                        }
                        else if(result.invoice[i].status == "Acc"){
                            bodyTable += '<td><span class="label label-success">Process By Accounting</span></td>';
                        }
                        else if(result.invoice[i].status == "payment_pch"){
                            bodyTable += '<td><span class="label label-primary"><i class="fa fa-check"></i> Payment Requested</span></td>';
                        }
                        else{
                            bodyTable += '<td></td>';
                        }
                    }
                    else if ("{{ Auth::user()->role_code == 'E - Purchasing'}}"){
                        if (result.invoice[i].status == "Open") {
                            bodyTable += '<td><a href="javascript:void(0)" class="btn btn-xs btn-success" onClick="check('+result.invoice[i].id+')" style="margin-right:5px;color:white" title="Check Tanda Terima"><i class="fa fa-check"></i></a> <a href="{{ url("edit/invoice") }}/'+result.invoice[i].id+'" class="btn btn-primary btn-xs" style="margin-right:5px;color:white" data-toggle="tooltip" title="Edit Tanda Terima"><i class="fa fa-pencil"></i></a> <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="rejectConfirmation('+result.invoice[i].id+')" style="margin-right:5px;color:white" title="Reject Tanda Terima"><i class="fa fa-rotate-left"></i> Reject</a></td>';
                        }
                        else if(result.invoice[i].status == "checked_pch"){
                            bodyTable += '<td><span class="label label-success"><i class="fa fa-check"></i> Confirmed</span></td>';
                        }
                        else if(result.invoice[i].status == "payment_pch"){
                            bodyTable += '<td><span class="label label-primary"><i class="fa fa-check"></i> Payment Requested</span></td>';
                        }
                        else{
                            bodyTable += '<td></td>';
                        }
                    }
                    else{
                        bodyTable += '<td></td>';
                    }
                    bodyTable += '</tr>';
                }
                $('#bodyTableInvoice').append(bodyTable);

                var table = $('#TableInvoice').DataTable({
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