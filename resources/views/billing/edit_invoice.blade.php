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
                      <form class="form-horizontal form-material mx-2" role="form" method="post" action="{{url('update/invoice')}}" enctype="multipart/form-data">
                          <input type="hidden" value="{{csrf_token()}}" name="_token" />
                          
                          <div class="form-group">
                              <label for="pic" class="col-md-12">Vendor</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="supplier_name" name="supplier_name" value="{{$invoice->supplier_name}}" disabled>
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-md-12 mb-0">Kwitansi</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="kwitansi" name="kwitansi" required="" value="{{$invoice->kwitansi}}">
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-md-12 mb-0">Invoice</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="tagihan" name="tagihan" required="" value="{{$invoice->tagihan}}">
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-md-12 mb-0">Surat Jalan</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="surat_jalan" name="surat_jalan" required="" value="{{$invoice->surat_jalan}}">
                              </div>
                          </div>
                           <div class="form-group">
                              <label class="col-md-12 mb-0">Faktur Pajak</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="faktur_pajak" name="faktur_pajak" required="" value="{{$invoice->faktur_pajak}}">
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-12">Purchase Order</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="purchase_order" name="purchase_order" required="" value="{{$invoice->purchase_order}}">
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-md-12 mb-0">Catatan</label>
                              <div class="col-md-12">
                                  <textarea rows="5" class="form-control ps-0 form-control-line" id="note" name="note">{{$invoice->note}}</textarea>
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-12">Mata Uang</label>
                              <div class="col-sm-12 border-bottom">
                                  <select class="form-select shadow-none border-0 ps-0 form-control-line" id="currency" name="currency" required="">
                                      <option value=""></option>
                                      @if($invoice->currency == "USD")
                                      <option value="USD" selected="">USD</option>
                                      <option value="IDR">IDR</option>
                                      <option value="JPY">JPY</option>
                                      @elseif($invoice->currency == "IDR")
                                      <option value="USD">USD</option>
                                      <option value="IDR" selected="">IDR</option>
                                      <option value="JPY">JPY</option>
                                      @elseif($invoice->currency == "JPY")
                                      <option value="USD">USD</option>
                                      <option value="IDR">IDR</option>
                                      <option value="JPY" selected="">JPY</option>
                                      @endif
                                  </select>
                              </div>
                          </div>

                           <div class="form-group">
                              <label class="col-sm-12">Jumlah DPP (Dasar Pengenaan Pajak)</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="amount" name="amount" required="" onchange="withPPN()" value="{{$invoice->amount}}">
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
                                  <input type="text" class="form-control ps-0 form-control-line" id="amount_interface" name="amount_interface" readonly="" value="{{$invoice->amount_total}}">
                                  <input type="hidden" class="form-control ps-0 form-control-line" id="amount_total" name="amount_total" readonly="" value="{{$invoice->amount_total}}">
                                  
                              </div>
                          </div>

                          <div class="form-group">
                              <label for="pic" class="col-md-12">Updated By</label>
                              <div class="col-md-12">
                                  <input type="text" class="form-control ps-0 form-control-line" id="updated_by" name="updated_by" value="{{Auth::user()->name}}" disabled>
                              </div>
                          </div>

                          <div class="form-group">
                              <div class="col-sm-12 d-flex">
                                  <input type="hidden" id="id_edit" name="id_edit" value="{{$invoice->id}}">
                                  <button class="btn btn-success mx-auto mx-md-0 text-white" type="submit">Submit Data</button>
                                  &nbsp;
                                  <button class="btn btn-danger mx-auto mx-md-0 text-white">Back</button>
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
  <script>
    $(function () {
      $('.select2').select2()
    });

    jQuery(document).ready(function() {
      if ("{{$invoice->ppn}}" == "on")  {
        $('#ppn').prop('checked', true);
      }else{
        $('#ppn').prop('checked', false);
      }
    });

    function withPPN() {
    var returns = '';
    $("input[name='ppn']:checked").each(function (i) {
      returns = $(this).val();
    });


    var amount = $('#amount').val();
    var amount_total = 0;
    if (returns == 'on') {  
      amount_total = parseInt(amount) + parseInt(amount*0.1)
    }
    else{
      amount_total = parseInt(amount);
    }
    $('#amount_total').val(amount_total);
    $('#amount_interface').val(amount_total.toLocaleString('de-DE'));
  }

  </script>
  @stop

