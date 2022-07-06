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
                <a class="btn btn-success pull-right" style="width: 100%;color: white" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create Payment Request</a>
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
                            <table class="table user-table no-wrap" id="TablePayment">
                                <thead>
                                    <tr>
                                        <th style="background-color: #757ce8;">#</th>
                                        <th style="background-color: #757ce8;">Payment Date</th>
                                        <th style="background-color: #757ce8;">Vendor</th>
                                        <!-- <th style="background-color: #757ce8;">Payment Term</th> -->
                                        <th style="background-color: #757ce8;">Due Date</th>
                                        <th style="background-color: #757ce8;">Kind Of Material</th>
                                        <th style="background-color: #757ce8;">Amount</th>
                                        <th style="background-color: #757ce8;">Status</th>
                                        <th style="background-color: #757ce8;">Action</th>
                                    </tr>
                                </thead>
                               <tbody id="bodyTablePayment">
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
     </div>
 </div>

 <div class="modal fade" id="modalNew">
    <div class="modal-dialog" style="max-width: 1200px !important">
        <div class="modal-content">
            <div class="modal-header" style="padding-top: 0;">
                <div class="row">
                    <input type="hidden" id="id_edit">
                    
                    <button type="button" class="close" aria-label="Close" style="text-align: right;margin-right: 10px;" onclick="closemodal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center><h3 style="font-weight: bold; padding: 3px;" id="modalNewTitle"></h3></center>
                    
                    <div class="col-md-6">


                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="date_payment" class="col-sm-12 control-label">Submission Date<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" value="<?= date('d-M-Y') ?>" disabled="">
                                <input type="text" class="form-control" value="{{ date('Y-m-d') }}" id="date_payment" name="date_payment" style="display: none;">
                        
                            </div>

                        </div>
                        
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="supplier_code" class="col-sm-12 control-label">Vendor Name<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <select class="form-control select4" id="supplier_code" name="supplier_code" data-placeholder='Choose Supplier Name' style="width: 100%" onchange="getSupplier(this)">
                                    <option value="">&nbsp;</option>
                                    @foreach($vendor as $ven)
                                    <option value="{{$ven->supplier_code}}">{{$ven->supplier_code}} - {{$ven->supplier_name}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" class="form-control" id="supplier_name" name="supplier_name" readonly="">
                            </div>
                        </div>
                        
                        <div class="col-md-12" style="margin-bottom: 5px">
                            <label for="Currency" class="col-sm-12 control-label">Currency<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <select class="form-control select4" id="currency" name="currency" data-placeholder='Choose Currency' style="width: 100%">
                                    <option value="">&nbsp;</option>
                                    <option value="USD">USD</option>
                                    <option value="IDR">IDR</option>
                                    <option value="JPY">JPY</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="payment_term" class="col-sm-12 control-label">Payment Term<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <select class="form-control select4" id="payment_term" name="payment_term" data-placeholder='Choose Payment Term' style="width: 100%">
                                    <option value="">&nbsp;</option>
                                    @foreach($payment_term as $pt)
                                    <option value="{{$pt->payment_term}}">{{$pt->payment_term}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="payment_due_date" class="col-sm-12 control-label">Due Date<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control pull-right datepicker" id="payment_due_date" name="payment_due_date">
                            </div>
                        </div> 
                        
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="amount" class="col-sm-12 control-label">Amount<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="amount" name="amount" readonly="">
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 5px">
                            <label for="material" class="col-sm-12 control-label">Kind Of Material<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <select class="form-control select4" id="kind_of" name="kind_of" data-placeholder='Type Material' style="width: 100%">
                                    <option value="">&nbsp;</option>
                                    <option value="Raw Materials">Raw Materials</option>
                                    <option value="Other (PPH)">Other (PPH)</option>
                                    <option value="Constool">Constool</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 5px">
                            <label for="document" class="col-sm-12 control-label">Attached Documents</label>
                            <div class="col-sm-12">

                                <label class="container">
                                    Local
                                    <input type="checkbox" id="doc_local" name="check_payment" class="check_payment" value="local">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container">
                                    Import
                                    <input type="checkbox" id="doc_import" name="check_payment" class="check_payment" value="import">
                                    <span class="checkmark"></span>
                                </label>

                                <label class="container">
                                    Invoice
                                    <input type="checkbox" id="doc_invoice" name="check_payment" class="check_payment" value="invoice">
                                    <span class="checkmark"></span>
                                </label>

                                <label class="container">
                                    Receipt
                                    <input type="checkbox" id="doc_receipt" name="check_payment" class="check_payment" value="receipt">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container">
                                    Surat Jalan
                                    <input type="checkbox" id="doc_surat_jalan" name="check_payment" class="check_payment" value="surat_jalan">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container">
                                    Faktur Pajak
                                    <input type="checkbox" id="doc_faktur_pajak" name="check_payment" class="check_payment" value="faktur_pajak">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding:0" style="">
                        <div class="row detail_create" style="padding:20px">
                            <div class="col-md-2" style="padding:5px;">
                                <b>No Invoice</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Amount (DPP)</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>PPN</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>Jenis PPH</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Amount (Jasa)</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>PPH (%)</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Net Payment</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>Aksi</b>
                            </div>

                            <input type="text" name="lop" id="lop" value="1" hidden>

                            <div class="col-md-2" style="padding:5px;">
                                <select class="form-control select2" data-placeholder="Choose Invoice" name="invoice1" id="invoice1" style="width: 100% height: 35px;" onchange="pilihInvoice(this)" required="">
                                </select>
                            </div>
                            
                            <div class="col-md-2" style="padding:5px;">
                                <input type="hidden" class="form-control" id="invoice_number1" name="invoice_number1" required="">
                                <input type="text" class="form-control" id="amount1" name="amount1" required="">
                            </div>

                            <div class="col-md-1" style="padding:5px;">
                                <input type="text" class="form-control" id="amountppn1" name="amountppn1">
                                <input type="hidden" class="form-control" id="ppn1" name="ppn1">
                            </div>

                            <div class="col-md-1" style="padding:5px;">
                                <!-- <input type="text" class="form-control" id="type_pph1" name="type_pph1" required=""> -->
                                <select class="form-control select2" data-placeholder="Choose Type PPH" name="typepph1" id="typepph1" style="width: 100% height: 35px;" onchange="pilihPPH(this)" required="">
                                    <option value=""></option>
                                    <option value="all">All</option>
                                    <option value="partial">Partial</option>
                                    <option value="none">None</option>
                                </select>
                            </div>


                            <div class="col-md-2" style="padding:5px;">
                                <input type="text" class="form-control" id="amount_jasa1" name="amount_jasa1" required="">
                            </div>

                            <div class="col-md-1" style="padding:5px;">
                                <input type="text" class="form-control" id="pph1" name="pph1" onkeyup="getTotal(this.id)">
                            </div>


                             <div class="col-md-2" style="padding:5px;">
                                <input type="text" class="form-control" id="amount_final1" name="amount_final1" required="">
                            </div>

                            <div class="col-md-1" style="padding:5px;">
                                <a type="button" class="btn btn-success" onclick='tambah("tambah","lop");'><i class='fa fa-plus' ></i></a>
                            </div>  

                        
                            <div id="tambah"></div>
                        </div>

                         <div class="row detail_edit" style="padding:20px">
                            <div class="col-md-2" style="padding:5px;">
                                <b>No Invoice</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Amount (DPP)</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Jenis PPH</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Amount (Jasa)</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>PPH (%)</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Net Payment</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>Aksi</b>
                            </div>
                        </div>
                        <div class="row detail_edit" id="modalDetailBodyEdit" style="padding:20px">
                        </div>

                        <div id="tambah2" style="padding-left:20px;padding-right: 20px;">
                            <input type="text" name="lop2" id="lop2" value="1" hidden="">
                            <input type="text" name="looping" id="looping" hidden="">
                        </div>
                            
                    </div>
                    <div class="col-md-12">
                        <a class="btn btn-success pull-right" onclick="Save('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
                        <a class="btn btn-info pull-right" onclick="Save('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


 <div class="modal fade" id="modaldelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="modaldeletehide()">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                Apakah anda ingin menghapus payment request ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="modaldeletehide()">Cancel</button>
                <a id="a" name="modalButtonDelete" type="button"  onclick="delete_payment_request(this.id)" class="btn btn-danger" style="color:white">Delete</a>
            </div>
        </div>
    </div>
</div>

 <!-- <div class="modal modal-danger fade" id="modalreject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Konfirmasi Hapus Data</h4>
          </div>
          <div class="modal-body">
            Apakah anda yakin ingin menghapus Form PR Ini ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <a id="a" name="modalButton" href="" type="button"  onclick="deletePR(this.id)" class="btn btn-danger">Delete</a>
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

        // $("#main-wrapper").attr("data-sidebartype","mini-sidebar");
        // document.getElementById("main-wrapper").setAttribute('data-sidebartype','mini-sidebar');
        // document.getElementById("logo-yamaha").setAttribute('height','20px');
        // document.getElementById("logo-yamaha").style.setProperty('height', '20px', 'important');
        // document.getElementsByClassName('logo-icon')[0].style.setProperty('padding-left', '0px', 'important');
        // document.getElementsByClassName('hide-menu')[0].style.setProperty('display', 'none', 'important');
        // document.getElementsByClassName('sidebar-item active selected')[0].style.setProperty('width', '65px', 'important');
     
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

    function getSupplier(elem){

        invoice_list = "";
        payment_list = "";
        supplier_name = "";

        var isi = elem.value;
        var invoice_number = $.parseJSON('<?php echo $invoice; ?>');
        
        $("#invoice1").html('');
        $("#payment_term").html('');

        invoice_list += '<option value=""></option>';
        payment_list += '<option value=""></option>';
        
        for(var i = 0; i < invoice_number.length;i++){

            payment_list += '<option value="'+invoice_number[i].payment_term+' selected">'+invoice_number[i].payment_term+'</option>';
            if (isi === '') {
                invoice_list += '<option value=""></option>';
            }else{
                if (invoice_number[i].supplier_code == isi) {
                    supplier_name = invoice_number[i].supplier_name;;
                    invoice_list += '<option value="'+invoice_number[i].id_tagihan+'">'+invoice_number[i].tagihan+'</option>';
                }

            }
        }
        $("#invoice1").append(invoice_list);
        $("#payment_term").append(payment_list);
        $('#supplier_name').val(supplier_name);
       // console.log(payment_list)
    }


    function pilihInvoice(elem)
    {
        var no = elem.id.match(/\d/g);
        no = no.join("");

        $.ajax({
            url: "{{ url('fetch/payment_request/detail') }}?invoice="+elem.value,
            method: 'GET',
            success: function(data) {
                var json = data,
                obj = JSON.parse(json);
                $('#invoice_number'+no).val(obj.invoice).attr('readonly', true);
                $('#amount'+no).val(obj.amount).attr('readonly', true);
                
                var amount_ppn = 0;
                
                if (obj.ppn == "on") {
                    amount_ppn = 0.1*parseInt(obj.amount);
                    $('#amountppn'+no).val(amount_ppn).attr('readonly', true);
                }else{
                    amount_ppn = 0;
                }
                $('#ppn'+no).val(obj.ppn).attr('readonly', true);
            }
        });

        // alert(sel.value);
    }

    function pilihPPH(elem){
        var num = elem.id.match(/\d/g);
        num = num.join("");

        var isi = elem.value;

        var amount = $('#amount'+num).val();
        var ppn = $('#ppn'+num).val();

        if (isi == "all") {
            $("#amount_jasa"+num).val(amount).attr('readonly',true);
            $("#pph"+num).val('').attr('readonly',false);
            $("#amount_final"+num).val('').attr('readonly',false);
        }
        else if(isi == "partial"){
            $("#amount_jasa"+num).val('').attr('readonly',false);
            $("#pph"+num).val('').attr('readonly',false);
            $("#amount_final"+num).val('').attr('readonly',false);
        }
        else{
            $("#amount_jasa"+num).val('').attr('readonly',true);
            $("#pph"+num).val('').attr('readonly',true);

            var amount = $('#amount'+num).val();
            var ppn = $('#ppn'+num).val();

            var amount_final = 0;
            
             if (ppn == "on") {
                amount_final = parseInt(amount) + parseInt(amount*0.1)
                $("#amount_final"+num).val(amount_final).attr('readonly',true);
            }else{
                amount_final = parseInt(amount);
                $("#amount_final"+num).val(amount_final).attr('readonly',true);
            }


            var amount_total = 0;

            for (var i = 1; i < no; i++) {
                amount_total += parseInt($("#amount_final"+i).val());
            }

             $("#amount").val(amount_total).attr('readonly',true);
            
        }
    }

    function getTotal(elem){
        var num = elem.match(/\d/g);
        num = num.join("");
        var isi = elem.value;

        var amount_total = 0;
        
        for (var i = 1; i < no; i++) {

            var amount = $('#amount'+i).val();
            var ppn = $('#ppn'+i).val();
            var amount_jasa = $('#amount_jasa'+i).val();
            var pph = $('#pph'+i).val();

            var amount_final = 0;

            var typepph = $('#typepph'+i).val();

            if (typepph == "all" || typepph == "partial") {
                if (ppn == "on") {
                    amount_final = parseInt(amount) + parseInt(amount*0.1) - (parseInt(pph) * parseInt(amount_jasa)/100);
                    amount_total += amount_final;
                }
                else{
                    amount_final = parseInt(amount) - (parseInt(pph) * parseInt(amount_jasa)/100);
                    amount_total += amount_final;
                }

                $("#amount_final"+i).val(amount_final).attr('readonly',true);
            }else{
                if (ppn == "on") {
                    amount_final = parseInt(amount) + parseInt(amount*0.1)
                    amount_total += amount_final;
                }else{
                    amount_final = parseInt(amount);
                    amount_total += amount_final;
                }
            }
        }

        $("#amount").val(amount_total).attr('readonly',true);
        


    }


    function Save(id){  
        $('#loading').show();

        if(id == 'new'){
            if($("#payment_date").val() == "" || $('#supplier_code').val() == null || $('#currency').val() == "" || $('#payment_term').val() == "" || $('#payment_due_date').val() == "" || $('#amount').val() == "" || $('#kind_of').val() == ""){
                
                $('#loading').hide();
                openErrorGritter('Error', "Please fill field with (*) sign.");
                return false;
            }

            var checked_payment = "";
            var ck = [];

            $.each($(".check_payment"), function(key, value) {
                if($(this).is(":checked")){
                    ck.push(value.value);
                }
            });

            checked_payment = ck.toString();

            var formData = new FormData();
            formData.append('payment_date', $("#date_payment").val());
            formData.append('supplier_code', $("#supplier_code").val());
            formData.append('supplier_name', $("#supplier_name").val());
            formData.append('currency', $("#currency").val());
            formData.append('payment_term', $("#payment_term").val());
            formData.append('payment_due_date', $("#payment_due_date").val());
            formData.append('amount', $("#amount").val());
            formData.append('kind_of', $("#kind_of").val());
            formData.append('attach_document', checked_payment);
            // formData.append('file_attach', $('#file_attach').prop('files')[0]);
            formData.append('jumlah', no);
            
            for (var i = 1; i < no; i++) {
                formData.append('invoice'+i, $("#invoice"+i).val());
                formData.append('invoice_number'+i, $("#invoice_number"+i).val());
                formData.append('amount'+i, $("#amount"+i).val());
                formData.append('ppn'+i, $("#ppn"+i).val());
                formData.append('typepph'+i, $("#typepph"+i).val());
                formData.append('amount_service'+i, $("#amount_jasa"+i).val());
                formData.append('pph'+i, $("#pph"+i).val());
                formData.append('amount_final'+i, $("#amount_final"+i).val());
            }

            $.ajax({
                url:"{{ url('create/payment_request') }}",
                method:"POST",
                data:formData,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success:function(data)
                {
                    if (data.status) {
                        openSuccessGritter('Success', data.message);
                        $('#loading').hide();
                        $('#modalNew').modal('hide');
                        clearNew();
                        fetchData();
                    }else{
                        openErrorGritter('Error!',data.message);
                        $('#loading').hide();
                        audio_error.play();
                    }

                }
            });
        }
        else{
            if($("#payment_date").val() == "" || $('#supplier_code').val() == null || $('#currency').val() == "" || $('#payment_term').val() == "" || $('#payment_due_date').val() == "" || $('#amount').val() == "" || $('#kind_of').val() == ""){
                
                $('#loading').hide();
                openErrorGritter('Error', "Please fill field with (*) sign.");
                return false;
            }


            var checked_payment = "";
            var ck = [];

            $.each($(".check_payment"), function(key, value) {
                if($(this).is(":checked")){
                    ck.push(value.value);
                }
            });

            checked_payment = ck.toString();

            var formData = new FormData();
            formData.append('id_edit', $("#id_edit").val());
            formData.append('payment_date', $("#payment_date").val());
            formData.append('supplier_code', $("#supplier_code").val());
            formData.append('supplier_name', $("#supplier_name").val());
            formData.append('currency', $("#currency").val());
            formData.append('payment_term', $("#payment_term").val());
            formData.append('payment_due_date', $("#payment_due_date").val());
            formData.append('amount', $("#amount").val());
            formData.append('attach_document', checked_payment);
            formData.append('kind_of', $("#kind_of").val());
            
            // formData.append('file_attach', $("#file_attach").prop('files')[0]);

            $.ajax({
                url:"{{ url('edit/payment_request') }}",
                method:"POST",
                data:formData,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success:function(data)
                {
                    if (data.status) {
                        openSuccessGritter('Success', data.message);
                        audio_ok.play();
                        $('#loading').hide();
                        $('#modalNew').modal('hide');
                        clearNew();
                        fetchData();
                    }else{
                        openErrorGritter('Error!',data.message);
                        $('#loading').hide();
                        audio_error.play();
                    }

                }
            });
        }
    }

    function clearNew(){
        $('#id_edit').val('');
        $("#payment_date").val('');
        $("#supplier_code").val('').trigger('change');
        $("#supplier_name").val('');
        $('#currency').val('').trigger('change');
        $("#payment_term").val('').trigger('change');
        $("#payment_due_date").val('').trigger('change');
        $('#amount').val('');
        $("#kind_of").val('').trigger('change');
    }

    function fetchData() {
        $.get('{{ url("fetch/payment_request") }}',  function(result, status, xhr){
            if(result.status){
                $('#TablePayment').DataTable().clear();
                $('#TablePayment').DataTable().destroy();
                $("#bodyTablePayment").html('');
                var listTableBody = '';
                
                $.each(result.payment, function(key, value){
                    listTableBody += '<tr>';
                    listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
                    listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.payment_date))+'</td>';
                    listTableBody += '<td style="width:3%;">'+value.supplier_code+' - '+value.supplier_name+'</td>';
                    // listTableBody += '<td style="width:2%;">'+value.payment_term+'</td>';
                    listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.payment_due_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.kind_of+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.amount.toLocaleString()+'</td>';

                   
                    if (value.posisi == 'user') {
                        listTableBody += '<td style="width:0.1%;"><span class="label label-danger">Not Sent</span></td>';
                    }
                    else if (value.posisi == 'manager'){
                        listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval Manager</span></td>';
                    }
                    else if (value.posisi == 'dgm'){
                        listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval DGM</span></td>';
                    }
                    else if (value.posisi == 'gm'){
                        listTableBody += '<td style="width:0.1%;"><span class="label label-warning">Approval GM</span></td>';
                    }
                    else if (value.posisi == 'acc'){
                        listTableBody += '<td style="width:0.1%;"><span class="label label-info">Diverifikasi Accounting</span></td>';
                    }
                    else{
                        listTableBody += '<td style="width:0.1%;"><span class="label label-success">Diterima Accounting</span></td>';
                    }

                    if (value.posisi == "user")
                    {
                        listTableBody += '<td style="width:2%;"><center><button class="btn btn-md btn-primary" onclick="newData(\''+value.id+'\')"><i class="fa fa-edit"></i> </button>  <a class="btn btn-md btn-warning" target="_blank" href="{{ url("report/payment_request") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a> <button class="btn btn-md btn-success" data-toggle="tooltip" title="Send Email" style="margin-right:5px;" onclick="sendEmail(\''+value.id+'\')"><i class="fa fa-envelope"></i></button><a href="javascript:void(0)" class="btn btn-md btn-danger" onClick="delete_payment('+value.id+')" style="margin-right:5px;color:white" title="Delete Payment Request"><i class="fa fa-trash"></i></a></center></td>';
                    }

                    else{
                        listTableBody += '<td style="width:2%;"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/payment_request") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a></center></td>';
                    }

                    listTableBody += '</tr>';
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

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
    var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

    function newData(id){

        if(id == 'new'){

            $('#modalNewTitle').text('Create Payment Request');
            $('#newButton').show();
            $('#updateButton').hide();
            clearNew();
            $('#modalNew').modal('show');

            $('.detail_create').show();
            $('.detail_edit').hide();

        }
        else{
            $('#newButton').hide();
            $('#updateButton').show();
            $('.detail_create').hide();
            $('.detail_edit').show()

            var data = {
                id:id
            }
            $.get('{{ url("detail/payment_request") }}', data, function(result, status, xhr){
                if(result.status){

                    $('#supplier_code').html('');
                    $('#payment_term').html('');
                    $('#currency').html('');
                    $('#kind_of').html('');

                    var supplier_code = "";
                    var payment_term = "";
                    var currency = "";
                    var kind_of = "";

                    $('#payment_date').val(result.payment.payment_date);

                    $.each(result.vendor, function(key, value){
                        if(value.supplier_code == result.payment.supplier_code){
                            supplier_code += '<option value="'+value.supplier_code+'" selected>'+value.supplier_code+' - '+value.supplier_name+'</option>';
                        }
                        else{
                            supplier_code += '<option value="'+value.supplier_code+'">'+value.supplier_code+' - '+value.supplier_name+'</option>';
                        }
                    });

                    $('#supplier_code').append(supplier_code);
                    $('#supplier_name').val(result.payment.supplier_name);

                    if(result.payment.currency == "USD"){
                        currency += '<option value="USD" selected>USD</option>';
                        currency += '<option value="IDR">IDR</option>';
                        currency += '<option value="JPY">JPY</option>';
                    }
                    else if (result.payment.currency == "IDR"){
                        currency += '<option value="USD">USD</option>';
                        currency += '<option value="IDR" selected>IDR</option>';
                        currency += '<option value="JPY">JPY</option>';
                    }
                    else if (result.payment.currency == "JPY"){
                        currency += '<option value="USD">USD</option>';
                        currency += '<option value="IDR">IDR</option>';
                        currency += '<option value="JPY" selected>JPY</option>';
                    }

                    $('#currency').append(currency);

                    $.each(result.payment_term, function(key, value){
                        if(value.payment_term == result.payment.payment_term){
                            payment_term += '<option value="'+value.payment_term+'" selected>'+value.payment_term+'</option>';
                        }
                        else{
                            payment_term += '<option value="'+value.payment_term+'">'+value.payment_term+'</option>';
                        }
                    });

                    $('#payment_term').append(payment_term);

                    $('#payment_due_date').val(result.payment.payment_due_date);
                    $('#amount').val(result.payment.amount);

                    if (result.payment.kind_of == "Raw Materials"){
                        kind_of += '<option value="Raw Materials" selected>Raw Materials</option>';
                        kind_of += '<option value="Other (PPH)">Other (PPH)</option>';
                        kind_of += '<option value="Constool">Constool</option>';
                    }
                    else if (result.payment.kind_of == "Other (PPH)"){
                        kind_of += '<option value="Raw Materials">Raw Materials</option>';
                        kind_of += '<option value="Other (PPH)" selected>Other (PPH)</option>';
                        kind_of += '<option value="Constool">Constool</option>';
                    }
                    else if (result.payment.kind_of == "Constool"){
                        kind_of += '<option value="Raw Materials">Raw Materials</option>';
                        kind_of += '<option value="Other (PPH)">Other (PPH)</option>';
                        kind_of += '<option value="Constool" selected>Constool</option>';
                    }

                    $('#kind_of').append(kind_of);

                    var attach = [];
                    var type = [];

                    attach = result.payment.attach_document.split(",");
                    $("input[name='check_payment']").each(function (i) {
                        type[i] = $(this).val();
                        $('.check_payment')[i].checked = false;
                    });
                    for (var i  = 0;i < attach.length; i++) {
                        for (var j  = 0;j < type.length; j++) {
                            if(type[j] == attach[i]){
                                $('.check_payment')[j].checked = true;
                            }
                        }
                    }


                    var ids = [];
                    $('#modalDetailBodyEdit').html('');

                    $.each(result.payment_detail, function(key, value) {

            
                        var tambah2 = "tambah2";
                        var lop2 = "lop2";

                        isi = "<div class='col-md-2' style='padding:5px;'>  <input type='text' class='form-control' id='invoice"+value.id+"'' name='invoice"+value.id+"' value='"+value.id_invoice+"' readonly=''> </div>"; 
                        isi += "<div class='col-md-2' style='padding:5px;'> <input type='hidden' class='form-control' id='invoice_number"+value.id+"'' name='invoice_number"+value.id+"' value='"+value.invoice+"' readonly=''> <input type='text' class='form-control' id='amount"+value.id+"'' name='amount"+value.id+"'' required=''  value='"+value.amount+"' readonly></div>";
                        isi += "<div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='ppn"+value.id+"'' name='ppn"+value.id+"'' value='"+value.ppn+"' readonly></div>";
                        isi += "<div class='col-md-1' style='padding:5px;'><input type='text' class='form-control' name='typepph"+value.id+"' id='typepph"+value.id+"' value='"+value.typepph+"' readonly=''></div>";
                        isi += "<div class='col-md-2' style='padding:5px;'> <input type='text' class='form-control' id='amount_jasa"+value.id+"' name='amount_jasa"+value.id+"' required='' value='"+value.amount_service+"' readonly=''> </div>";
                        isi += "<div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='pph"+value.id+"' name='pph"+value.id+"' value='"+value.pph+"' readonly=''> </div>";
                        isi += "<div class='col-md-2' style='padding:5px;'> <input type='text' class='form-control' id='amount_final"+value.id+"' name='amount_final"+value.id+"' required='' value='"+value.net_payment+"' readonly=''> </div>";
                        isi += "<div class='col-md-1' style='padding:5px;'><a href='javascript:void(0);' id='"+ value.id +"' onclick='deleteConfirmation(\""+ value.item_desc +"\","+value.id +");' class='btn btn-danger' data-toggle='modal' data-target='#modaldanger'><i class='fa fa-close'></i> </a> <button type='button' class='btn btn-success' onclick='tambah(\""+ tambah2 +"\",\""+ lop2 +"\");'><i class='fa fa-plus' ></i></button></div>";

                        ids.push(value.id);

                        $('#modalDetailBodyEdit').append(isi);

                        $(function () {
                            $('.select5').select2({
                                dropdownAutoWidth : true,
                                allowClear: true
                            });
                        })

                        $("#looping").val(ids);

                    });

                    $('#id_edit').val(result.payment.id);
                    $('#modalNewTitle').text('Update Payment Request');
                    $('#loading').hide();
                    $('#modalNew').modal('show');
                }
                else{
                    openErrorGritter('Error', result.message);
                    $('#loading').hide();
                    audio_error.play();
                }
            });
        }
    }

    function rejectConfirmation(id) {
        $('[name=modalButton]').attr("id",id);
    }

    function tambah(id,lop) {
        var id = id;

        var lop = "";

        if (id == "tambah"){
            lop = "lop";
        }else{
            lop = "lop2";
        }

        var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px;padding:0'><div class='row'><div class='col-md-2' style='padding:5px;'> <select class='form-control select3' data-placeholder='Choose Invoice' name='invoice"+no+"' id='invoice"+no+"' style='width: 100% height: 35px;' onchange='pilihInvoice(this)'> </select> </div><div class='col-md-2' style='padding:5px;'> <input type='text' class='form-control' id='amount"+no+"' name='amount"+no+"' required=''><input type='hidden' class='form-control' id='invoice_number"+no+"' name='invoice_number"+no+"' required=''></div><div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='amountppn"+no+"' name='amountppn"+no+"'><input type='hidden' class='form-control' id='ppn"+no+"' name='ppn"+no+"'> </div><div class='col-md-1' style='padding:5px;'> <select class='form-control select3' data-placeholder='Choose Type PPH' name='typepph"+no+"' id='typepph"+no+"' style='width: 100% height: 35px;' onchange='pilihPPH(this)' required=''> <option value=''></option><option value='all'>All</option> <option value='partial'>Partial</option> <option value='none'>None</option> </select> </div><div class='col-md-2' style='padding:5px;'> <input type='text' class='form-control' id='amount_jasa"+no+"' name='amount_jasa"+no+"' required=''> </div><div class='col-md-1' style='padding:5px;'> <input type='text' class='form-control' id='pph"+no+"' name='pph"+no+"' onkeyup='getTotal(this.id)'> </div><div class='col-md-2' style='padding:5px;'> <input type='text' class='form-control' id='amount_final"+no+"' name='amount_final"+no+"' required=''> </div><div class='col-md-1' style='padding:5px;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div></div>");


        $("#"+id).append(divdata);
        $("#invoice"+no).append(invoice_list);

        $('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd',
            todayHighlight:true
        });

        $(function () {
            $('.select3').select2({
                dropdownAutoWidth : true,
                dropdownParent: $("#"+id),
                allowClear:true
                // minimumInputLength: 3
            });
        })

        // $("#"+id).select2().trigger('change');
        document.getElementById(lop).value = no;
        no+=1;
    }

    function kurang(elem,lop) {

        var lop = lop;
        var ids = $(elem).parent('div').parent('div').parent('div').attr('id');
        var oldid = ids;
        $(elem).parent('div').parent('div').parent('div').remove();
        var newid = parseInt(ids) + 1;

        $("#"+newid).attr("id",oldid);
        $("#invoice"+newid).attr("name","invoice"+oldid);
        $("#invoice_number"+newid).attr("name","invoice_number"+oldid);
        $("#amount"+newid).attr("name","amount"+oldid);
        $("#amountppn"+newid).attr("name","amountppn"+oldid);
        $("#ppn"+newid).attr("name","ppn"+oldid);
        $("#pph"+newid).attr("name","pph"+oldid);
        $("#typepph"+newid).attr("name","typepph"+oldid);
        $("#amount_jasa"+newid).attr("name","amount_jasa"+oldid);
        $("#amount_final"+newid).attr("name","amount_final"+oldid);   

        $("#invoice"+newid).attr("id","invoice"+oldid);
        $("#invoice_number"+newid).attr("id","invoice_number"+oldid);
        $("#amount"+newid).attr("id","amount"+oldid);
        $("#amountppn"+newid).attr("id","amountppn"+oldid);
        $("#ppn"+newid).attr("id","ppn"+oldid);
        $("#pph"+newid).attr("id","pph"+oldid);
        $("#typepph"+newid).attr("id","typepph"+oldid);
        $("#amount_jasa"+newid).attr("id","amount_jasa"+oldid);
        $("#amount_final"+newid).attr("id","amount_final"+oldid);   

        no-=1;
        var a = no -1;

        for (var i =  ids; i <= a; i++) {   
            var newid = parseInt(i) + 1;
            var oldid = newid - 1;
            $("#"+newid).attr("id",oldid);
            $("#invoice"+newid).attr("name","invoice"+oldid);
            $("#invoice_number"+newid).attr("name","invoice_number"+oldid);
            $("#amount"+newid).attr("name","amount"+oldid);
            $("#amountppn"+newid).attr("name","amountppn"+oldid);
            $("#ppn"+newid).attr("name","ppn"+oldid);
            $("#pph"+newid).attr("name","pph"+oldid); 
            $("#typepph"+newid).attr("name","typepph"+oldid);
            $("#amount_jasa"+newid).attr("name","amount_jasa"+oldid);
            $("#amount_final"+newid).attr("name","amount_final"+oldid);  

            $("#invoice"+newid).attr("id","invoice"+oldid);
            $("#invoice_number"+newid).attr("id","invoice_number"+oldid);
            $("#amount"+newid).attr("id","amount"+oldid);
            $("#amountppn"+newid).attr("id","amountppn"+oldid);
            $("#ppn"+newid).attr("id","ppn"+oldid);
            $("#pph"+newid).attr("id","pph"+oldid);
            $("#typepph"+newid).attr("id","typepph"+oldid);
            $("#amount_jasa"+newid).attr("id","amount_jasa"+oldid);
            $("#amount_final"+newid).attr("id","amount_final"+oldid);  
        }

        document.getElementById(lop).value = a;
        getTotal("pph"+a);
    }

    // function getInvoiceList() {
    //     $.get('{{ url("fetch/payment_request/list") }}', function(result, status, xhr) {
    //         invoice_list += "<option></option> ";
    //         $.each(result.invoice, function(index, value){
    //             invoice_list += "<option value='"+value.tagihan+"'>"+value.tagihan+ "</option> ";
    //         });
    //         $('#invoice1').append(invoice_list);
    //     })
    // }

    function delete_payment(id) {
        $('#modaldelete').modal('show');
        $('[name=modalButtonDelete]').attr("id","delete_"+id);
    }

    function modaldeletehide(){
        $('#modaldelete').modal('hide');
    }

    function delete_payment_request(id){

        var id_delete = id.split("_");

        var data = {
            id:id_delete[1]
        }
        $("#loading").show();

        $.post('{{ url("delete/payment_request") }}', data, function(result, status, xhr){
            if (result.status == true) {
                openSuccessGritter("Success","Data Berhasil Dihapus");
                $("#loading").hide();
                modaldeletehide();
                fetchData();
            }
            else{
                openErrorGritter("Success","Data Gagal Dihapus");
            }
        });
    }

    function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Payment Request ini ke Manager?")) {
        return false;
      }
      else{
        $("#loading").show();
      }

      $.get('{{ url("email/payment_request") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
        $("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    function closemodal(){
        $('#modalNew').modal('hide');
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