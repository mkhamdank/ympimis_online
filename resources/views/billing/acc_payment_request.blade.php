    @extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/dataTables.bootstrap4.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">
<style type="text/css">
    thead>tr>th{
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
            <div class="col-md-3 col-8 align-self-right" style="text-align: right;">
            </div>
            <div class="col-md-3 col-8 align-self-right" style="text-align: right;">
                <!-- <a href="{{ url("/create/payment_request") }}" class="btn btn-primary"><i class="fa fa-plus"></i> <span class="hide-menu">Create Payment Request</span></a> -->
                <!-- <a class="btn btn-success pull-right" style="width: 100%;color: white" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create Payment Request</a> -->
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
                     <div class="card-title">
                        <div class="col-sm-12" style="margin-top:20px">
                            <span style="color:red"> Outstanding Invoice Without ID Payment </span>
                        </div>
                     </div>
                    <div class="card-body" style="padding:0.5 em">
                        <div class="table-responsive">
                            <table class="table user-table no-wrap" id="TablePayment">
                                <thead>
                                    <tr>
                                        <th style="background-color: #757ce8;">#</th>
                                        <th style="background-color: #757ce8;">Vendor</th>  
                                        <th style="background-color: #757ce8;">Invoice No</th>
                                        <th style="background-color: #757ce8;">Currency</th>
                                        <th style="background-color: #757ce8;">Amount</th>
                                        <th style="background-color: #757ce8;">PPN</th>
                                        <th style="background-color: #757ce8;">PPH</th>
                                        <th style="background-color: #757ce8;">Net Payment</th>
                                        <th style="background-color: #757ce8;">Payment Term</th>
                                        <th style="background-color: #757ce8;">Due Date</th>
                                        <th style="background-color: #757ce8;">Surat Jalan</th>
                                        <th style="background-color: #757ce8;">Faktur Pajak</th>
                                        <th style="background-color: #757ce8;">PO Number</th>
                                        <th style="background-color: #757ce8;">TT Date</th>
                                        <th style="background-color: #757ce8;">Dist Date Pch</th>
                                        <th style="background-color: #757ce8;">Dist Date Acc</th> 
                                        <th style="background-color: #757ce8;">File Invoice</th>                           
                                        <th style="background-color: #757ce8;">ID Payment</th>
                                        <!-- <th style="background-color: #757ce8;">Date Payment</th>   -->
                                    </tr>
                                </thead>
                               <tbody id="bodyTablePayment">
                                    
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-success" style="float:right;margin-top: 20px;" onclick="cek()"><i class="fa fa-save"></i> Save</button>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="card">
                     <div class="card-title">
                        <div class="col-sm-12" style="margin-top:20px">
                            <span style="color:blue"> Outstanding Invoice Not Jurnal</span>
                        </div>
                     </div>
                    <div class="card-body" style="padding:0.5 em">
                        <div class="table-responsive">
                            <table class="table user-table no-wrap" id="TablePaymentAfter">
                                <thead>
                                    <tr>
                                        <th style="background-color: #757ce8;">#</th>
                                        <th style="background-color: #757ce8;">Vendor</th>  
                                        <th style="background-color: #757ce8;">Invoice No</th>
                                        <th style="background-color: #757ce8;">Currency</th>
                                        <th style="background-color: #757ce8;">Amount</th>
                                        <th style="background-color: #757ce8;">PPN</th>
                                        <th style="background-color: #757ce8;">PPH</th>
                                        <th style="background-color: #757ce8;">Net Payment</th>
                                        <th style="background-color: #757ce8;">Payment Term</th>
                                        <th style="background-color: #757ce8;">Due Date</th>
                                        <th style="background-color: #757ce8;">Surat Jalan</th>
                                        <th style="background-color: #757ce8;">Faktur Pajak</th>
                                        <th style="background-color: #757ce8;">PO Number</th>
                                        <th style="background-color: #757ce8;">TT Date</th>
                                        <th style="background-color: #757ce8;">Dist Date Pch</th>
                                        <th style="background-color: #757ce8;">Dist Date Acc</th> 
                                        <th style="background-color: #757ce8;">File Invoice</th>                          
                                        <th style="background-color: #757ce8;">ID Payment</th>
                                        <!-- <th style="background-color: #757ce8;">Date Payment</th>   -->
                                    </tr>
                                </thead>
                               <tbody id="bodyTablePaymentAfter">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
     </div>
 </div>


 <!-- <div class="modal fade" id="modalcheck" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="modalcheckhide()">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                Input ID Payment Here
                <input type="text" id="payment" name="payment" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="modalcheckhide()">Cancel</button>
                <a id="a" name="modalButtonCheck" type="button"  onclick="checked(this.id)" class="btn btn-success">Check</a>
            </div>
        </div>
    </div>
  </div> -->

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
    var no = 2;
    invoice_list = "";

    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });


    jQuery(document).ready(function() {
        fetchData();
        fetchDataPayment();
    });


    // getInvoiceList();

   $('.datepicker').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true,   
    });

   $('.select2').select2({
        dropdownAutoWidth : true,
        allowClear: true
    });

    $(function () {
        $('.select4').select2({
            allowClear:true,
            dropdownAutoWidth : true,
            // tags: true,
            dropdownParent: $('#modalNew')
        });
    })

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function getFormattedDate(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        
        return day + '-' + monthNames[month] + '-' + year;
    }

    var counts = 0;

    function fetchData() {
        $.get('{{ url("fetch/accounting/payment") }}',  function(result, status, xhr){
            if(result.status){
                $('#TablePayment').DataTable().clear();
                $('#TablePayment').DataTable().destroy();
                $("#bodyTablePayment").html('');
                var listTableBody = '';
                $.each(result.payment, function(key, value){
                    listTableBody += '<tr class="member">';
                    listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.supplier_name+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.invoice+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.currency+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.amount+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.ppn+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.pph+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.net_payment+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_term+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_due_date+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.surat_jalan+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.faktur_pajak+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.purchase_order+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.tt_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.dist_date_pch))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.status_gm.split('/')[1]))+'</td>';
                    // listTableBody += '<td style="width:2%;"><a href="javascript:void(0)" class="btn btn-xs btn-info" onClick="check('+value.id_payment_detail+')" style="margin-right:5px;color:white" title="Input ID Payment"><i class="fa fa-edit"></i></a> </td>';

                    listTableBody += '<td style="width:1%;"><a target="_blank" href="{{ url("files/invoice") }}/'+value.file+'"><i class="fa fa-paperclip"></i></a></td>';
                    listTableBody += '<input type="hidden" class="form-control" id="id_'+key+'" value="'+value.id_payment_detail+'">';
                    listTableBody += '<td style="width:2%;"><input type="text" class="form-control payment" id="payment_acc_'+key+'" name="payment_acc_'+key+'"></td>';
                    // listTableBody += '<td style="width:2%;"></td>';
                    listTableBody += '</tr>';
                    counts++;
                });

                $('#bodyTablePayment').append(listTableBody);

                var table = $('#TablePayment').DataTable({
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

    function fetchDataPayment() {
        $.get('{{ url("fetch/accounting/payment/after") }}',  function(result, status, xhr){
            if(result.status){
                $('#TablePaymentAfter').DataTable().clear();
                $('#TablePaymentAfter').DataTable().destroy();
                $("#bodyTablePaymentAfter").html('');
                var listTableBody = '';
                $.each(result.payment, function(key, value){
                    listTableBody += '<tr class="member">';
                    listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.supplier_name+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.invoice+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.currency+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.amount+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.ppn+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.pph+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.net_payment+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_term+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_due_date+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.surat_jalan+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.faktur_pajak+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.purchase_order+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.tt_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.dist_date_pch))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.status_gm.split('/')[1]))+'</td>';
                    listTableBody += '<td style="width:1%;"><a target="_blank" href="{{ url("files/invoice") }}/'+value.file+'"><i class="fa fa-paperclip"></i></a></td>';
                    listTableBody += '<td style="width:2%;">'+value.acc_payment+'</td>';
                    listTableBody += '</tr>';
                    counts++;
                });

                $('#bodyTablePaymentAfter').append(listTableBody);

                var table = $('#TablePaymentAfter').DataTable({
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

    // function check(id) {
    //     $('#modalcheck').modal('show');
    //     $('[name=modalButtonCheck]').attr("id","check_"+id);
    // }

    // function modalcheckhide(){
    //     $('#modalcheck').modal('hide');
    // }

    // function checked(id){

    //     var id_check = id.split("_");

    //     var data = {
    //         id:id_check[1]
    //     }

    //     $("#loading").show();

    //     $.post('{{ url("checked/invoice") }}', data, function(result, status, xhr){
    //         if (result.status == true) {
    //             openSuccessGritter("Success","Data Berha4sil Dihapus");
    //             $("#loading").hide();
    //             modalcheckhide();
    //             fetchData();
    //         }
    //         else{
    //             openErrorGritter("Success","Data Gagal Dihapus");
    //         }
    //     });
    // }

    function cek() {
        if (confirm('Apakah Anda yakin?')) {
            $('#loading').show();

            var id_payment = [];
            var payment = [];

            var formData = new FormData();

            for(var i = 0; i < counts;i++){
                id_payment.push($('#id_'+i).val());
                payment.push($('#payment_acc_'+i).val());
            }

            // $('.payment').each(function(i, obj) {
            //     var id = $(this).attr('id').split("_");
                
            //     id_payment.push(id[2]);
            //     formData.append('payment_acc_'+id[2], $(this).val());
            // });
            
            formData.append('id_payment', id_payment);
            formData.append('payment', payment);

            $.ajax({
                url:"{{ url('post/accounting/payment') }}",
                method:"POST",
                data:formData,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (response.status) {
                        $("#loading").hide();
                        openSuccessGritter("Success", "Data Berhasil Disimpan");
                        location.reload();
                    }else{
                        $("#loading").hide();
                        openErrorGritter("Error", "ID Payment Sudah ada");
                    }
                //     console.log(response.message);
                },
                error: function (response) {
                    console.log(response.message);
                },
            })
        }
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
    var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

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