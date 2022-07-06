@extends('layouts.master')
@section('header')

 <div class="page-breadcrumb" style="padding: 20px">
    <div class="row align-items-center">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="page-title mb-0 p-0">{{$title}}<span class="text-purple"> {{$title_jp}}</span></h3>
        </div>
    </div>
</div>
@endsection

@section('stylesheets')

<style type="text/css">

  .containers {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 15px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    padding-top: 6px;
  }

  /* Hide the browser's default checkbox */
  .containers input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
  }


  .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
    margin-top: 4px;
  }

  /* On mouse-over, add a grey background color */
  .containers:hover input ~ .checkmark {
    background-color: #ccc;
  }

  /* When the checkbox is checked, add a blue background */
  .containers input:checked ~ .checkmark {
    background-color: #2196F3;
  }

  /* Create the checkmark/indicator (hidden when not checked) */
  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
  }

  /* Show the checkmark when checked */
  .containers input:checked ~ .checkmark:after {
    display: block;
  }

  /* Style the checkmark/indicator */
  .containers .checkmark:after {
    left: 9px;
    top: 5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
  }
  </style>

@endsection

@section('content')
<section class="content">

  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif

  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
    <div class="container-fluid">
      <div class="row">          
          <div class="col-lg-12 col-xlg-12 col-md-12">
              <div class="card">
                  <div class="card-body">
                      <form class="form-horizontal form-material mx-2" role="form" method="post" action="{{url('post/upload_invoice')}}" enctype="multipart/form-data">
                          <input type="hidden" value="{{csrf_token()}}" name="_token" />
                          <div class="form-group">
                              <label class="col-md-12 mb-0">Tanggal</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line datepicker" value="<?= date('d-M-Y') ?>" disabled>
                                  <input type="hidden" class="form-control" value="{{date('Y-m-d')}}" id="tanggal" name="tanggal">
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="pic" class="col-md-12">PIC - Vendor</label>
                              <div class="col-md-12">
                                 <!-- {{$user->supplier_name}} -->
                                  <input type="text" class="form-control ps-0 form-control-line" value="{{Auth::user()->name}} - {{Auth::user()->company}}" disabled>
                                  <input type="hidden" id="pic" name="pic" value="{{Auth::user()->name}}">
                                  <input type="hidden" id="supplier_code" name="supplier_code" value="{{Auth::user()->remark}}">
                                  <input type="hidden" id="supplier_name" name="supplier_name" value="{{Auth::user()->company}}">
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-md-12 mb-0">Kwitansi</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="kwitansi" name="kwitansi">
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-md-12 mb-0">Invoice</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="tagihan" name="tagihan" required="">
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-md-12 mb-0">Surat Jalan</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="surat_jalan" name="surat_jalan" required="">
                              </div>
                          </div>
                           <div class="form-group">
                              <label class="col-md-12 mb-0">Faktur Pajak</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="faktur_pajak" name="faktur_pajak" required="">
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-12">Purchase Order</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="purchase_order" name="purchase_order" required="">
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-md-12 mb-0">Catatan</label>
                              <div class="col-md-12">
                                  <textarea rows="5" class="form-control ps-0 form-control-line" id="note" name="note"></textarea>
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-12">Mata Uang</label>
                              <div class="col-sm-12 border-bottom">
                                  <select class="form-select select2 shadow-none border-0 ps-0 form-control-line" id="currency" name="currency" required="" style="width:100%">
                                      <option value=""></option>
                                      <option value="USD">USD</option>
                                      <option value="IDR">IDR</option>
                                      <option value="JPY">JPY</option>
                                  </select>
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-12">Jumlah DPP (Dasar Pengenaan Pajak)</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="amount" name="amount" required="" onchange="withPPN()">
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-12">PPN</label>
                              <div class="col-md-12">
                                <label class="containers" onclick="withPPN()">Memakai PPN
                                  <input type="checkbox" id="ppn" name="ppn">
                                  <span class="checkmark"></span>
                                </label>
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-12">Jumlah (Amount Fix)</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="amount_interface" name="amount_interface" readonly="">
                                  <input type="hidden" class="form-control ps-0 form-control-line" id="amount_total" name="amount_total" readonly="">
                                  
                              </div>
                          </div>

                           <div class="form-group">
                              <label class="col-sm-12">File Lampiran</label>
                              <div class="col-md-12">
                                  <input type="file" class="form-control ps-0 form-control-line" id="lampiran" name="lampiran" required="">
                                  <span class="text-red">*File Kwitansi, Tagihan, Surat Jalan, Faktur Pajak, Dan PO Harus Dijadikan satu File PDF</span>
                              </div>
                          </div>

                          <div class="form-group">
                              <div class="col-sm-12 d-flex">
                                  <button class="btn btn-success mx-auto mx-md-0 text-white" type="submit">Submit Data</button>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
  </div>

  @endsection

  @section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ url("js/dataTables.bootstrap4.min.js") }}"></script>
<script src="{{ url("js/jquery.flot.min.js") }}"></script>

  <script type="text/javascript">
     $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

    $(function () {
      $('.select2').select2()
    });

    jQuery(document).ready(function() {

    });


  function withPPN() {
    var returns = '';
    $("input[name='ppn']:checked").each(function (i) {
            returns = $(this).val();
        });


    var amount = $('#amount').val();
    var amount_total = 0;
    if (returns == 'on') {  
      amount_total = parseInt(amount) + parseInt(amount*0.11)
    }
    else{
      amount_total = parseInt(amount);
    }
    $('#amount_total').val(amount_total);
    $('#amount_interface').val(amount_total.toLocaleString('de-DE'));
  }

  </script>
  @stop

