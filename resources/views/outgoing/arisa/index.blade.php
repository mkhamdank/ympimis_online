@extends('layouts.display')
@section('stylesheets')
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
		color: #FFD700;
	}
	thead {
		background-color: rgb(126,86,134);
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	#ngTemp {
		height:200px;
		overflow-y: scroll;
	}

	#ngList2 {
		height:600px;
		overflow-y: scroll;
		/*padding-top: 5px;*/
	}
	#loading, #error { display: none; }
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	.page-wrapper{
		padding-top: 0px;
	}
	.containers {
	  display: block;
	  position: relative;
	  padding-left: 20px;
	  margin-bottom: 6px;
	  cursor: pointer;
	  font-size: 22px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	}

	/* Hide the browser's default radio button */
	.containers input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	}

	/* Create a custom radio button */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	  border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.containers:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.containers input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.containers input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the indicator (dot/circle) */
	.containers .checkmark:after {
	 	top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}
</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid" style="padding-top: 10px;padding-left: 10px;padding-right: 10px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<input type="hidden" id="start_time" value="">
	<input type="hidden" id="incoming_check_code" value="">
	
	<div class="row" style="padding-left: 10px; padding-right: 10px;">
		<div class="col-md-4" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
				<tbody>
					<tr>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;width: 50%">Date</th>
						<th colspan="2" style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;width: 50%">Inspector</th>
					</tr>
					<tr>
						<td style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px;" id="date">{{date("Y-m-d")}}</td>
						<td style="background-color: #14213d; color: #fff; text-align: center; font-size:15px;" id="op">{{$inspector}}</td>
					</tr>
					
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							Material Number
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							Material Description
						</td>
					</tr>
					<tr>
						<td id="material_number" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 15px;">-
						</td>
						<td id="material_description" style="background-color: #14213d; text-align: center; color: white; font-size: 15px;">-
						</td>
					</tr>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							Alias
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							Part
						</td>
					</tr>
					<tr>
						<td id="material_alias" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 15px;">-
						</td>
						<td id="part" style="background-color: #14213d; text-align: center; color: white; font-size: 15px;">-
						</td>
					</tr>
					<tr>
						<th colspan="2" style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;width: 50%">QC Serial Number</th>
					</tr>
					<tr>
						<td colspan="2" style="background-color: #fca311; color: #14213d; text-align: center; font-size:20px;" id="serial_number">-</td>
					</tr>
					<tr>
						<th colspan="2" style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;width: 50%">Serial Number</th>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							QTY APPEARANCE (PCS)
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							QTY FUNCTIONAL (PCS)
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							QTY DIMENTIONAL (PCS)
						</td>
					</tr>
					<tr>
						<td>
							<input type="number" class="pull-right" name="qty_check_appearance" style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check_appearance" onchange="checkQty(this.id)" placeholder="Qty Appearance" >
						</td>
						<td>
							<input type="number" class="pull-right" name="qty_check_functional" style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check_functional" placeholder="Qty Functional" >
						</td>
						<td>
							<input type="number" class="pull-right" name="qty_check_dimensional" style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check_dimensional" placeholder="Qty Dimentional" >
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							LOT STATUS
						</td>
					</tr>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							<!-- <select style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d;background-color: green;color: white;font-weight: bold;" id="lot_status" data-placeholder="Lot Status">
								<option value="LOT OK">LOT OK</option>
								<option value="LOT OUT">LOT OUT</option>
							</select> -->
							<input type="text" class="pull-right" name="lot_status" style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d;background-color: green;color: white;font-weight: bold;" id="lot_status" placeholder="Lot Status" readonly="" value="LOT OK">
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="col-md-8" style="padding-right: 0;">
			<!-- <div class="row" style="padding-left: 15px;padding-right: 15px">
				<button class="btn btn-info pull-right" onclick="fetchDetailRecord()" style="width: 100%;font-weight: bold;font-size: 20px">Record</button>
			</div> -->
			<div id="ngList2">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1" id="tableAppearance">
					<thead>
						<tr>
							<th colspan="10" style="width: 10%; background-color: #5c6bc0;color:white; padding:0;font-size: 20px;">Appearance Check</th>
						</tr>
						<tr>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">Item</th>
							<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Standard</th>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Lower</th>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Upper</th>
							<th colspan="5" style="width: 5%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Result</th>
						</tr>
					</thead>
					<tbody id="bodyTableAppearance" style="background-color: white;color: black">
						
					</tbody>
				</table>

				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1" id="tableFunctional">
					<thead>
						<tr>
							<th id="titleFun" style="width: 10%; background-color: #cddc39;color:black; padding:0;font-size: 20px;">Functional Check</th>
						</tr>
						<tr>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">Item</th>
							<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Standard</th>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Lower</th>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Upper</th>
							<th id="resultTitleFun" style="width: 5%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Result</th>
						</tr>
					</thead>
					<tbody id="bodyTableFunctional" style="background-color: white;color: black">
						
					</tbody>
				</table>

				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1" id="tableDimensional">
					<thead>
						<tr>
							<th id="titleDim" style="width: 10%; background-color: #dc7739;color:white; padding:0;font-size: 20px;">Dimensional Check</th>
						</tr>
						<tr>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 3%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">Item</th>
							<th style="width: 2%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Standard</th>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Lower</th>
							<th style="width: 1%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Upper</th>
							<th id="resultTitleDim" style="width: 5%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Result</th>
						</tr>
					</thead>
					<tbody id="bodyTableDimensional" style="background-color: white;color: black">
						
					</tbody>
				</table>
			</div>
			<!-- <div id="ngTemp">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
					<thead>
						<tr>
							<th style="width: 30%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Nama NG</th>
							<th style="width: 10%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Qty</th>
							<th style="width: 10%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Status</th>
							<th style="width: 30%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Note</th>
							<th style="width: 20%; background-color: #d1d1d1; padding:0;font-size: 20px;" >Action</th>
						</tr>
					</thead>
					<tbody id="bodyNgTemp">
					</tbody>
				</table>
			</div> -->

			<div class="row">
				<div class="col-md-6" style="padding: 0px;padding-top: 10px;padding-right: 5px;padding-left: 10px">
					<button class="btn btn-danger" id="btn_cancel" onclick="cancelAll('button')" style="font-size: 25px;font-weight: bold;width: 100%">
						CANCEL
					</button>
				</div>
				<div class="col-md-6" style="padding: 0px;padding-top: 10px;padding-left: 5px;padding-right: 10px">
					<button class="btn btn-success" id="btn_confirm" onclick="confirmNgLog()" style="font-size: 25px;font-weight: bold;width: 100%">
						CONFIRM
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalOperator" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<div class="form-group">
						<label for="exampleInputEmail1">Nama Karyawan</label>
						<input class="form-control" style="width: 100%; text-align: center;" type="text" id="operator" placeholder="Masukkan Nama" required>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<!-- <div class="modal-header" style="background-color: #ffc654;text-align: center;">
				
			</div> -->
			<div class="modal-body">
				<div style="background-color: #ffc654;text-align: center;margin-bottom: 10px">
					<center style="padding: 5px;width: 100%"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;">PILIH PRODUCT</h4></center>
				</div>
				<!-- <div class="col-md-12"> -->
				<div class="row" id="div_product_choose">
					<?php $index = 1; ?>
					@foreach($product as $product)
					<div class="col-md-4">
						<button class="btn btn-primary" style="width: 100%;margin-bottom: 5px;font-size: 12px;background-color: {{$product->hexa_button}};color: black;border-color: green" id="btn_product_{{$index}}" onclick="getProduct(this.id)">{{$product->material_alias}} - {{$product->part}}</button>
						<input type="hidden" id="input_product_{{$index}}" value="{{$product->material_number}}_{{$product->material_description}}_{{$product->material_alias}}_{{$product->part}}">
					</div>
					<?php $index++ ?>
					@endforeach
				</div>
				<!-- </div> -->
				<div class="row" id="div_product_fix">
					<div class="col-md-12">
						<button class="btn btn-primary" style="width: 100%;margin-bottom: 5px;font-size: 15px;margin-top: 10px" id="btn_product_fix" onclick="changeProduct()">PN</button>
					</div>
				</div>

				<div style="background-color: #ffc654;text-align: center;margin-bottom: 10px">
					<center style="padding: 5px;width: 100%"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;">PILIH HASIL CEK</h4></center>
				</div>
				<div class="row" id="div_sernum_choose">
					<select class="form-control" id="kensa_serial_number" name="kensa_serial_number" data-placeholder="Pilih Serial Number" style="width: 100%">

					</select>
				</div>
				<div class="row" style="margin-top: 5px;margin-bottom: 5px">
					<div class="col-md-4 pull-right">
						<button style="width: 100%" class="btn btn-success btn-sm" onclick="addSerialNumber()">Add Serial Number</button>
					</div>
				</div>
				<table class="table table-bordered table-striped table-hover">
	        		<thead style="background-color: rgba(126,86,134,.7);color: white">
	        			<tr>
	        				<th style="width: 6%;background-color: rgba(126,86,134,.7);color: white">Serial Number</th>
	        				<th style="width: 2%;background-color: rgba(126,86,134,.7);color: white">Qty Check</th>
	        				<th style="width: 2%;background-color: rgba(126,86,134,.7);color: white">OK</th>
	        				<th style="width: 2%;background-color: rgba(126,86,134,.7);color: white">NG</th>
	        				<th style="width: 2%;background-color: rgba(126,86,134,.7);color: white">Action</th>
	        			</tr>
	        		</thead>
	        		<tbody id="bodyTableInput">
	        		</tbody>
	        	</table>

	        	<div style="background-color: #ffc654;text-align: center;margin-bottom: 10px">
					<center style="padding: 5px;width: 100%"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;">MASUKKAN QUANTITY</h4></center>
				</div>
	        	<div class="row" id="">
					<div class="col-md-12">
						<table class="table table-bordered table-striped table-hover">
			        		<thead style="background-color: rgba(126,86,134,.7);color: white">
			        			<tr>
			        				<th style="width: 6%;background-color: rgba(126,86,134,.7);color: white">QTY APPEARANCE (PCS)</th>
			        				<th style="width: 6%;background-color: rgba(126,86,134,.7);color: white">QTY FUNCTIONAL (PCS)</th>
			        				<th style="width: 6%;background-color: rgba(126,86,134,.7);color: white">QTY DIMENSIONAL (PCS)</th>
			        			</tr>
			        		</thead>
			        		<tbody id="bodyTableInputs">
			        			<tr>
			        				<td><input type="number" class="pull-right" name="qty_check_app" style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check_app" onchange="checkQty(this.id)" placeholder="Qty Appearance" ></td>
			        				<td><input type="number" class="pull-right" name="qty_check_fun" style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check_fun" placeholder="Qty Functional" ></td>
			        				<td><input type="number" class="pull-right" name="qty_check_dim" style="height: 50px;font-size: 1.7vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check_dim" placeholder="Qty Dimensional" ></td>
			        			</tr>
			        		</tbody>
			        	</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="col-md-12" style="margin:0px">
					<div class="row">
						<button class="btn btn-success" style="width: 100%;margin-bottom: 5px;font-size: 20px;margin-top: 10px;padding-right: 0px" id="btn_product_fix" onclick="confirmProduct()">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<!-- <script src="{{ url("js/jqbtk.js") }}"></script> -->

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	var hour;
    var minute;
    var second;
    var intervalTime;
    var intervalUpdate;

    var serial_number = [];
    var count_serial_number = [];

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 30%;align-items:center"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:1.5vw; height: 50px;width:100%"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-primary" style="font-size:1.5vw; width:40px;background-color:#b5ffa8;color:black"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:1.5vw; width: 100%;background-color:#ffb84d;color:black"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	var all_check = 0;
	var lot_ok = 0;
	var lot_out = 0;
	var ng_result_checkbox = 0;
	var ng_result_counting = 0;
	var ng_result_value = 0;
	var ng_result_all = 0;

	jQuery(document).ready(function() {

		// $('#modalOperator').modal('show');
		// $('#modalOperator').modal('show');

		// $('#modalOperator').on('shown.bs.modal', function () {
		// 	$('#operator').focus();
		// });

		// $("#operator").val('');
		// $("#operator").focus();
		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		cancelAll('ready');

		$('#kensa_serial_number').select2({
			dropdownParent: $("#div_sernum_choose")
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.numpad2').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		// $('#invoice').keyboard({
	 //        usePreview: false,
	 //        change: function(e, kb) {
	 //          table.search(kb.el.value).draw();
	 //        }
	 //      });
		// $('#material_number').keyboard({
	 //        usePreview: false,
	 //        change: function(e, kb) {
	 //          table.search(kb.el.value).draw();
	 //        }
	 //      });
		// $('#note_ng').keyboard({
	 //        usePreview: false,
	 //        change: function(e, kb) {
	 //          table.search(kb.el.value).draw();
	 //        }
	 //      });
		// $('#ng_search').keyboard();

		document.getElementById('lot_status').style.backgroundColor = "green";
		document.getElementById('lot_status').style.color = "white";
		$("#lot_status").val('LOT OK').trigger('change');
		var lot_status_select = document.getElementById('lot_status');
		lot_status_select.setAttribute('onchange','changeLotStatus(this.value)');
		ng_list();
		all_check = 0;
	});

	function changeLotStatus(status) {
		if (status === 'LOT OK') {
			document.getElementById('lot_status').style.backgroundColor = "green";
			document.getElementById('lot_status').style.color = "white";
			$("#lot_status").val('LOT OK').trigger('change');
		}else{
			document.getElementById('lot_status').style.backgroundColor = "red";
			document.getElementById('lot_status').style.color = "white";
			$("#lot_status").val('LOT OUT').trigger('change');
		}
	}

	function cancelAll(param) {
		$('#material_number').html('-');
		// $('#op').html('-');
		$('#material_description').html('-');
		$('#material_alias').html('-');
		$('#part').html('-');
		$('#div_product_choose').show();
		$('#div_product_fix').hide();
		$('#qty_check_appearance').val('');
		$('#qty_check_functional').val('5');
		$('#qty_check_dimensional').val('4');
		// $('#appearance_ok').val('0');
		// $('#functional_ok').val('5');
		// $('#appearance_ng').val('0');
		// $('#functional_ng').val('0');
		$('#bodyTableAppearance').html('');
		$('#bodyTableFunctional').html('');
		$('#bodyTableDimensional').html('');
		// $('#bodyTableSerialNumber').html('');
		$('#bodyTableInput').html('');

		$('#qty_check_app').val('');
		$('#qty_check_fun').val('5');
		$('#qty_check_dim').val('5');
		serial_number = [];
		count_serial_number = 0;
		$('#modalProduct').modal('show');
	}


	function getProduct(id) {
		var nomor = id.split('_')[2];
		var product = $('#input_product_'+nomor).val();
		$('#btn_product_fix').html(product.split('_')[0]+' - '+product.split('_')[1]+' - '+product.split('_')[2]+' - '+product.split('_')[3]);
		$('#div_product_fix').show();
		$('#div_product_choose').hide();

		$('#kensa_serial_number').html('');
		var kensa_serial_number = '';

		var data = {
			material_number:product.split('_')[0]
		}

		$.get('{{ url("fetch/kensa/arisa/serial_number") }}', data, function(result, status, xhr){
			if(result.status){
				for(var i = 0; i< result.kensa_serial_number.length;i++){
					kensa_serial_number += '<option value="'+result.kensa_serial_number[i].serial_number+'_'+result.kensa_serial_number[i].total_ok+'_'+result.kensa_serial_number[i].total_ng+'_'+result.kensa_serial_number[i].qty_check+'">'+result.kensa_serial_number[i].serial_number+' (Check:'+result.kensa_serial_number[i].qty_check+') (OK:'+result.kensa_serial_number[i].total_ok+') (NG:'+result.kensa_serial_number[i].total_ng+')</option>';
				}
				$('#kensa_serial_number').append(kensa_serial_number);
				$('#kensa_serial_number').val('').trigger('change');
				$('#loading').hide();
			}else{
				openErrorGritter('Error!',result.message);
				$('#loading').hide();
			}
		});

		all_check = 0;
		
	}

	function changeProduct(){
		$('#btn_product_fix').html('PN');
		$('#div_product_fix').hide();
		$('#div_product_choose').show();

	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	// $('#operator').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		$('#op').html($('#operator').val());
	// 		updateKensaCode();
	// 		$('#modalOperator').modal('hide');
	// 		$('#modalProduct').modal('show');
	// 	}
	// });

	var index_check_box = 0;
	var index_check_box_appearance = 0;
	var index_counting_appearance = 0;
	var index_check_box_functional = 0;
	var index_counting_functional = 0;
	var index_values_functional = 0;
	var index_values_dimensional = 0;

	var id_check_box_appearance = [];
	var id_check_box_functional = [];
	var id_counting_appearance = [];
	var id_counting_functional = [];
	var id_values_functional = [];
	var id_values_dimensional = [];

	var point_check_id = [];

	function addSerialNumber() {

		if ($('#kensa_serial_number').val() == "" || $('#kensa_serial_number').val() == null) {
			audio_error.play();
			openErrorGritter('Error!','Input Semua Data.');
			return false;
		}

		if (serial_number.length > 0 || $('#kensa_serial_number').val() != null) {
			if($.inArray($('#kensa_serial_number').val().split('_')[0], serial_number) != -1){
				audio_error.play();
				openErrorGritter('Error!','Serial Number already exists.');
				return false;
			}
		}

		var serial_numbers = $('#kensa_serial_number').val().split('_')[0];
		var ok = $('#kensa_serial_number').val().split('_')[1];
		var ng = $('#kensa_serial_number').val().split('_')[2];
		var qty_check = $('#kensa_serial_number').val().split('_')[3];


		var tableSernum = "";

		all_check = all_check + (parseInt(ok)+parseInt(ng));

		tableSernum += '<tr id="'+serial_numbers+'">';
		tableSernum += '<td>'+serial_numbers+'</td>';
		tableSernum += '<td>'+qty_check+'</td>';
		tableSernum += '<td>'+ok+'</td>';
		tableSernum += '<td>'+ng+'</td>';
		tableSernum += "<td><a href='javascript:void(0)' onclick='remSerialNumber(id)' id='"+serial_numbers+"' class='btn btn-danger btn-sm' style='margin-right:5px;'><i class='fa fa-trash'></i></a></td>";

		
		$('#bodyTableInput').append(tableSernum);
		serial_number.push(serial_numbers);

		count_serial_number += 1;

		var data = {
			vendor:'ARISA',
		}

		$.get('{{ url("fetch/inspection_level") }}', data, function(result, status, xhr){
			if(result.status){
				for(var i = 0; i < result.inspection_levels.length;i++){
					if (all_check >= result.inspection_levels[i].lot_size_lower && all_check <= result.inspection_levels[i].lot_size_upper) {
						$('#qty_check_app').val(result.inspection_levels[i].sample_size);
						lot_ok = result.inspection_levels[i].lot_ok;
						lot_out = result.inspection_levels[i].lot_out;
					}
				}
			}else{
				openErrorGritter('Error!',result.message);
				$('#loading').hide();
			}
		});

		$('#kensa_serial_number').val('').trigger('change');
		openSuccessGritter('Success!','Add Serial Number Success');
	}

	function remSerialNumber(id){
		for(var i = 0; i < serial_number.length;i++){
			if (serial_number[i] === id) {
				serial_number.splice( i, 1 );
			}
		}
		count_serial_number -= 1;
		$('#'+id).remove();
	}

	function confirmProduct() {

		if ($('#btn_product_fix').text() == '-' || $('#kensa_serial_number').val() == '' || serial_number.length == 0 || $('#qty_check_app').val() == '' || $('#qty_check_fun').val() == '' || $('#qty_check_dim').val() == '') {
			openErrorGritter('Error!','Pilih Product dan Serial Number');
			return false;
		}

		$('#qty_check_appearance').val($('#qty_check_app').val());
		$('#qty_check_functional').val($('#qty_check_fun').val());
		$('#qty_check_dimensional').val($('#qty_check_dim').val());

		$('#material_number').html($('#btn_product_fix').text().split(' - ')[0]);
		$('#material_description').html($('#btn_product_fix').text().split(' - ')[1]);
		$('#material_alias').html($('#btn_product_fix').text().split(' - ')[2]);
		$('#part').html($('#btn_product_fix').text().split(' - ')[3]);



		var data = {
			material_number:$('#btn_product_fix').text().split(' - ')[0],
			vendor:'ARISA'
		}

		$.get('{{ url("fetch/outgoing/arisa/point_check") }}', data, function(result, status, xhr){
			if(result.status){
				$('#bodyTableAppearance').html('');
				$('#bodyTableFunctional').html('');
				$('#bodyTableDimensional').html('');
				var tableDataAppearance = '';
				var tableDataFunctional = '';
				var tableDataDimensional = '';

				index_check_box = 0;
				index_check_box_appearance = 0;
				index_counting_appearance = 0;
				index_check_box_functional = 0;
				index_counting_functional = 0;
				index_values_functional = 0;
				index_values_dimensional = 0;
				id_check_box_appearance = [];
				id_check_box_functional = [];
				id_counting_appearance = [];
				id_counting_functional = [];
				id_values_functional = [];
				id_values_dimensional = [];
				point_check_id = [];
				var index = 0;

				var adacounting = 0;

				for(var i = 0; i < result.point_check.length; i++){
					if (result.point_check[i].point_check_type == 'FUNCTIONAL CHECK') {
						if(result.point_check[i].remark == 'counting'){
							adacounting++;
						}
					}
				}

				for(var i = 0; i < result.point_check.length; i++){
					if (index % 2 == 0) {
						backgroundColor = '#f7f7f7';
					}else{
						backgroundColor = '#ffffff';
					}
					if (result.point_check[i].point_check_type == 'APPEARANCE CHECK') {
						if (result.point_check[i].remark == 'checkbox') {
							tableDataAppearance += '<tr style="border-bottom:2px solid red;background-color:'+backgroundColor+'">';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_index+'</td>';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_name+'</td>';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_standard+'</td>';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_lower+'</td>';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_upper+'</td>';
							tableDataAppearance += '<td style="font-size:20px;border:1px solid black;width:1%;">';
							tableDataAppearance += '<label class="containers">&#9711;';
							  tableDataAppearance += '<input type="radio" class="messageCheckbox" name="condition_0_'+result.point_check[i].id+'_'+index_check_box_appearance+'" id="condition_0_'+result.point_check[i].id+'_'+index_check_box_appearance+'" value="OK" onclick="checkCondition(0,\''+result.point_check[i].id+'\',\''+index_check_box_appearance+'\',\''+result.point_check[i].remark+'\')">';
							  tableDataAppearance += '<span class="checkmark" id="checkmarkok_0_'+result.point_check[i].id+'_'+index_check_box_appearance+'"></span>';
							tableDataAppearance += '</label>';
							tableDataAppearance += '<label class="containers">&#9747;';
							  tableDataAppearance += '<input type="radio" class="messageCheckbox" name="condition_0_'+result.point_check[i].id+'_'+index_check_box_appearance+'" id="condition_0_'+result.point_check[i].id+'_'+index_check_box_appearance+'" value="NG" onclick="checkCondition(0,\''+result.point_check[i].id+'\',\''+index_check_box_appearance+'\',\''+result.point_check[i].remark+'\')">';
							  tableDataAppearance += '<span class="checkmark" id="checkmarkng_0_'+result.point_check[i].id+'_'+index_check_box_appearance+'"></span>';
							tableDataAppearance += '</label>';
							tableDataAppearance += '</td>';
							tableDataAppearance += '</tr>';
							index_check_box_appearance++;
							id_check_box_appearance.push(result.point_check[i].id);
							point_check_id.push(result.point_check[i].id);
						}else if(result.point_check[i].remark == 'counting'){
							tableDataAppearance += '<tr style="border-bottom:2px solid red;background-color:'+backgroundColor+'">';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_index+'</td>';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_name+'</td>';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_standard+'</td>';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_lower+'</td>';
							tableDataAppearance += '<td>'+result.point_check[i].point_check_upper+'</td>';
							tableDataAppearance += '<td id="minus" onclick="minus(0,'+parseInt(result.point_check[i].id)+','+index_counting_appearance+')" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>';
							tableDataAppearance += '<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span class="countingCheck" id="count_0_'+parseInt(result.point_check[i].id)+'_'+index_counting_appearance+'">0</span></td>';
							tableDataAppearance += '<td id="plus" onclick="plus(0,'+parseInt(result.point_check[i].id)+','+index_counting_appearance+')" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>';
							tableDataAppearance += '</tr>';
							index_counting_appearance++;
							id_counting_appearance.push(result.point_check[i].id);
							point_check_id.push(result.point_check[i].id);
						}
					}

					if (result.point_check[i].point_check_type == 'FUNCTIONAL CHECK') {
						var funcounting = 0;
						if (adacounting > 0) {
							var colspan_result = 'colspan="3"';
						}else{
							var colspan_result = '';
						}
						if (result.point_check[i].remark == 'checkbox') {
							tableDataFunctional += '<tr style="border-bottom:2px solid red;background-color:'+backgroundColor+'">';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_index+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_name+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_standard+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_lower+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_upper+'</td>';
							for(var j = 1; j < parseInt($('#qty_check_fun').val())+1; j++){
								tableDataFunctional += '<td '+colspan_result+' style="font-size:20px;border:1px solid black;width:1%;">';
								tableDataFunctional += '<label class="containers">&#9711;';
								  tableDataFunctional += '<input class="messageCheckbox" type="radio" name="condition_'+j+'_'+result.point_check[i].id+'_'+index_check_box_functional+'" id="condition_'+j+'_'+result.point_check[i].id+'_'+index_check_box_functional+'" value="OK" onclick="checkCondition(\''+j+'\',\''+result.point_check[i].id+'\',\''+index_check_box_functional+'\',\''+result.point_check[i].remark+'\')">';
								  tableDataFunctional += '<span class="checkmark" id="checkmarkok_'+j+'_'+result.point_check[i].id+'_'+index_check_box_functional+'"></span>';
								tableDataFunctional += '</label>';
								tableDataFunctional += '<label class="containers">&#9747;';
								  tableDataFunctional += '<input class="messageCheckbox" type="radio" name="condition_'+j+'_'+result.point_check[i].id+'_'+index_check_box_functional+'" id="condition_'+j+'_'+result.point_check[i].id+'_'+index_check_box_functional+'" value="NG" onclick="checkCondition(\''+j+'\',\''+result.point_check[i].id+'\',\''+index_check_box_functional+'\',\''+result.point_check[i].remark+'\')">';
								  tableDataFunctional += '<span class="checkmark" id="checkmarkng_'+j+'_'+result.point_check[i].id+'_'+index_check_box_functional+'"></span>';
								tableDataFunctional += '</label>';
								tableDataFunctional += '</td>';
								index_check_box_functional++;
								id_check_box_functional.push(result.point_check[i].id);
							}
							tableDataFunctional += '</tr>';
							point_check_id.push(result.point_check[i].id);
						}else if(result.point_check[i].remark == 'counting'){
							funcounting++;
							tableDataFunctional += '<tr style="border-bottom:2px solid red;background-color:'+backgroundColor+'">';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_index+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_name+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_standard+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_lower+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_upper+'</td>';
							for(var j = 1; j < parseInt($('#qty_check_fun').val())+1;j++){
								tableDataFunctional += '<td id="minus" onclick="minus('+j+','+parseInt(result.point_check[i].id)+','+index_counting_functional+')" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>';
								tableDataFunctional += '<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span class="countingCheck" id="count_'+j+'_'+parseInt(result.point_check[i].id)+'_'+index_counting_functional+'">0</span></td>';
								tableDataFunctional += '<td id="plus" onclick="plus('+j+','+parseInt(result.point_check[i].id)+','+index_counting_functional+')" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>';
								index_counting_functional++;
								id_counting_functional.push(result.point_check[i].id);
							}
							tableDataFunctional += '</tr>';
							point_check_id.push(result.point_check[i].id);
						}else if(result.point_check[i].remark == 'values'){
							tableDataFunctional += '<tr style="border-bottom:2px solid red;background-color:'+backgroundColor+'">';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_index+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_name+'</td>';
							tableDataFunctional += '<td>'+result.point_check[i].point_check_standard+'</td>';
							tableDataFunctional += '<td class="valueCheckLower">'+result.point_check[i].point_check_lower+'</td>';
							tableDataFunctional += '<td class="valueCheckUpper">'+result.point_check[i].point_check_upper+'</td>';
							for(var j = 1; j < parseInt($('#qty_check_fun').val())+1; j++){
								tableDataFunctional += '<td '+colspan_result+'><input type="number" class="pull-right valueCheck" name="values_'+j+'_'+result.point_check[i].id+'_'+index_values_functional+'" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="values_'+j+'_'+result.point_check[i].id+'_'+index_values_functional+'" placeholder="'+j+'" onchange="checkValue('+result.point_check[i].point_check_upper+','+result.point_check[i].point_check_lower+','+j+','+result.point_check[i].id+','+index_values_functional+')"></td>';
								index_values_functional++;
								id_values_functional.push(result.point_check[i].id);
							}
							tableDataFunctional += '</tr>';
							point_check_id.push(result.point_check[i].id);
						}

						if (funcounting > 0) {
							$('#resultTitleFun').prop('colspan',parseInt($('#qty_check_fun').val())*3);
							$('#titleFun').prop('colspan',(parseInt($('#qty_check_fun').val())+5)*3);
						}else{
							$('#resultTitleFun').prop('colspan',$('#qty_check_fun').val());
							$('#titleFun').prop('colspan',parseInt($('#qty_check_fun').val())+5);
						}
					}

					if (result.point_check[i].point_check_type == 'DIMENSIONAL CHECK') {
						if(result.point_check[i].remark == 'values'){
							tableDataDimensional += '<tr style="border-bottom:2px solid red;background-color:'+backgroundColor+'">';
							tableDataDimensional += '<td>'+result.point_check[i].point_check_index+'</td>';
							tableDataDimensional += '<td>'+result.point_check[i].point_check_name+'</td>';
							tableDataDimensional += '<td>'+result.point_check[i].point_check_standard+'</td>';
							tableDataDimensional += '<td class="valueCheckLower">'+result.point_check[i].point_check_lower+'</td>';
							tableDataDimensional += '<td class="valueCheckUpper">'+result.point_check[i].point_check_upper+'</td>';
							for(var j = 1; j < parseInt($('#qty_check_dim').val())+1; j++){
								tableDataDimensional += '<td><input type="number" class="pull-right valueCheck" name="values_'+j+'_'+result.point_check[i].id+'_'+index_values_dimensional+'" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="values_'+j+'_'+result.point_check[i].id+'_'+index_values_dimensional+'" placeholder="'+j+'" onchange="checkValue('+result.point_check[i].point_check_upper+','+result.point_check[i].point_check_lower+','+j+','+result.point_check[i].id+','+index_values_dimensional+')"></td>';
								index_values_dimensional++;
								id_values_dimensional.push(result.point_check[i].id);
							}
							tableDataDimensional += '</tr>';
							point_check_id.push(result.point_check[i].id);
						}

						$('#resultTitleDim').prop('colspan',$('#qty_check_dim').val());
						$('#titleDim').prop('colspan',parseInt($('#qty_check_dim').val())+5);
					}
					index++;
				}
				$('#bodyTableAppearance').append(tableDataAppearance);
				$('#bodyTableFunctional').append(tableDataFunctional);
				$('#bodyTableDimensional').append(tableDataDimensional);

				$('.numpad3').numpad({
					hidePlusMinusButton : true,
					decimalSeparator : '.'
				});

				$('#serial_number').html(serial_number[0]);

				// var serial_number_product = '';
				// $('#bodyTableSerialNumber').html('');
				// for(var m = 0; m < serial_number.length;m++){
				// 	serial_number_product += '<tr style="background-color:white">';
				// 	serial_number_product += '<td style="font-size:20px">'+serial_number[m]+'</td>';
				// 	serial_number_product += '</tr>';
				// }
				// $('#bodyTableSerialNumber').append(serial_number_product);
				// updateKensaCode();
			}else{
				openErrorGritter('Error!',result.message);
				$('#loading').hide();
			}
		});

		$('#modalProduct').modal('hide');
	}

	function checkCondition(count,id,index,check_type) {
		var result_check = '';
		var checkedValue = []; 
		var inputElements = document.getElementsByClassName('messageCheckbox');
		for(var i=0; inputElements[i]; ++i){
		      if(inputElements[i].checked){
		           checkedValue.push(inputElements[i].value);
		      }
		}
		if (checkedValue.join(',').match(/NG/gi)) {
	   		ng_result_checkbox++;
	    }else{
	    	ng_result_checkbox = 0;
	    }
		$("input[name='condition_"+count+"_"+id+"_"+index+"']:checked").each(function (i) {
            result_check = $(this).val();
            // console.log($(this).val());
        });
        // console.log(result_check);
        if (result_check == 'OK') {
        	document.getElementById('checkmarkok_'+count+'_'+id+'_'+index).style.backgroundColor = "#1ed44e";
        	document.getElementById('checkmarkng_'+count+'_'+id+'_'+index).style.backgroundColor = "#fff";
        	// if (ng_result < 0) {
        	// 	ng_result = 0;
        	// }else{
        	// 	ng_result--;
        	// }
        	// if (ng_result > lot_ok) {
    //     		document.getElementById('lot_status').style.backgroundColor = "red";
				// document.getElementById('lot_status').style.color = "white";
				// $("#lot_status").val('LOT OUT');
        	// }else{
    //     		document.getElementById('lot_status').style.backgroundColor = "green";
				// document.getElementById('lot_status').style.color = "white";
				// $("#lot_status").val('LOT OK');
        	// }
        }else if (result_check == 'NG') {
        	document.getElementById('checkmarkok_'+count+'_'+id+'_'+index).style.backgroundColor = "#fff";
        	document.getElementById('checkmarkng_'+count+'_'+id+'_'+index).style.backgroundColor = "#d41e1e";
        	// ng_result++;
        	// if (ng_result > lot_ok) {
    //     		document.getElementById('lot_status').style.backgroundColor = "red";
				// document.getElementById('lot_status').style.color = "white";
				// $("#lot_status").val('LOT OUT');
        	// }
        }
   //      if (ng_result_checkbox > 0) {
   //      	document.getElementById('lot_status').style.backgroundColor = "red";
			// document.getElementById('lot_status').style.color = "white";
			// $("#lot_status").val('LOT OUT');
   //      }else{
   //      	document.getElementById('lot_status').style.backgroundColor = "green";
			// document.getElementById('lot_status').style.color = "white";
			// $("#lot_status").val('LOT OK');
   //      }
   			checkAll();
	}

	function checkQty(id) {
		var val = $('#'+id).val();
		$('#'+id.split('_')[2]+'_ok').val(val);
	}

	function checkValue(upper,lower,number,id,index) {
		var val = $('#values_'+number+'_'+id+'_'+index).val();
		if (parseFloat(val) >= parseFloat(lower) && parseFloat(val) <= parseFloat(upper)) {
			document.getElementById('values_'+number+'_'+id+'_'+index).style.backgroundColor = '#a0ff9e';
			// if (ng_result < 0) {
   //      		ng_result = 0;
   //      	}else{
   //      		ng_result--;
   //      	}
   //      	if (ng_result > 0) {
   //      		document.getElementById('lot_status').style.backgroundColor = "red";
			// 	document.getElementById('lot_status').style.color = "white";
			// 	$("#lot_status").val('LOT OUT');
   //      	}else{
   //      		document.getElementById('lot_status').style.backgroundColor = "green";
			// 	document.getElementById('lot_status').style.color = "white";
			// 	$("#lot_status").val('LOT OK');
   //      	}
		}else{
			document.getElementById('values_'+number+'_'+id+'_'+index).style.backgroundColor = '#ff9e9e';
			// ng_result++;
   //      	if (ng_result > 0) {
   //      		document.getElementById('lot_status').style.backgroundColor = "red";
			// 	document.getElementById('lot_status').style.color = "white";
			// 	$("#lot_status").val('LOT OUT');
   //      	}
		}
		var countValues = document.getElementsByClassName('valueCheck');
		var countValuesUpper = document.getElementsByClassName('valueCheckUpper');
		var countValuesLower = document.getElementsByClassName('valueCheckLower');
		// console.log(countValuesLower.length);
		// console.log(countValues.length);
		ng_result_value = 0;
		var index = 0;
		for(var i = 0; i < countValues.length;i++){
			var lowers = 0;
			var uppers = 0;
			if (i % 5 == 0 && i != 0) {
				index++;
			}
			// console.log(i % 5);
			if (countValues[i].value.length != 0) {
				if (parseFloat(countValues[i].value) >= parseFloat(countValuesLower[index].innerHTML) && parseFloat(countValues[i].value) <= parseFloat(countValuesUpper[index].innerHTML)) {

				}else{
					ng_result_value++;
				}
			}
		}

		// if (ng_result_value > 0) {
		// 	document.getElementById('lot_status').style.backgroundColor = "red";
		// 	document.getElementById('lot_status').style.color = "white";
		// 	$("#lot_status").val('LOT OUT');
		// }else{
		//      document.getElementById('lot_status').style.backgroundColor = "green";
		// 	document.getElementById('lot_status').style.color = "white";
		// 	$("#lot_status").val('LOT OK');
		// }
		checkAll();
	}

	function plus(counts,id,index){
		var count = $('#count_'+counts+'_'+id+'_'+index).text();
		if ($('#material_description').text() == "-") {
			openErrorGritter('Error!','Isi Semua Data.');
		}else{
			$('#count_'+counts+'_'+id+'_'+index).text(parseInt(count)+1);

			var countValue = document.getElementsByClassName('countingCheck');
			ng_result_counting = 0;
			for(var i = 0; i < countValue.length;i++){
				ng_result_counting = ng_result_counting + parseInt(countValue[i].innerHTML);
			}

			// if (ng_result_counting > lot_ok) {
			// 	document.getElementById('lot_status').style.backgroundColor = "red";
			// 	document.getElementById('lot_status').style.color = "white";
			// 	$("#lot_status").val('LOT OUT');
			// }
			checkAll();
		}
	}

	function minus(counts,id,index){
		var count = $('#count_'+counts+'_'+id+'_'+index).text();
		if ($('#material_description').text() == "-") {
			openErrorGritter('Error!','Isi Semua Data.');
		}else{
			if(count > 0)
			{
				// $('#total_ok').val(parseInt($('#total_ok').val())+1);
				// $('#total_ng').val(parseInt($('#total_ng').val())-1);
				// $('#ng_ratio').val(((parseInt($('#total_ng').val())/parseInt($('#qty_check').val()))*100).toFixed(1));
				$('#count_'+counts+'_'+id+'_'+index).text(parseInt(count)-1);
				var countValue = document.getElementsByClassName('countingCheck');
				ng_result_counting = 0;
				for(var i = 0; i < countValue.length;i++){
					ng_result_counting = ng_result_counting + parseInt(countValue[i].innerHTML);
				}

				// if (ng_result_counting > lot_ok) {
				// 	document.getElementById('lot_status').style.backgroundColor = "red";
				// 	document.getElementById('lot_status').style.color = "white";
				// 	$("#lot_status").val('LOT OUT');
				// }else{
				// 	document.getElementById('lot_status').style.backgroundColor = "green";
				// 	document.getElementById('lot_status').style.color = "white";
				// 	$("#lot_status").val('LOT OK');
				// }
				checkAll();
			}
		}
	}

	function checkAll() {
		if (ng_result_counting > lot_ok || ng_result_value > 0 || ng_result_checkbox > 0) {
			document.getElementById('lot_status').style.backgroundColor = "red";
			document.getElementById('lot_status').style.color = "white";
			$("#lot_status").val('LOT OUT');
		}else{
			document.getElementById('lot_status').style.backgroundColor = "green";
			document.getElementById('lot_status').style.color = "white";
			$("#lot_status").val('LOT OK');
		}
	}

	function updateKensaCode() {
		$.get('{{ url("fetch/final/serial_number/arisa") }}',  function(result, status, xhr){
			if(result.status){
				$('#serial_number').html(result.serial_number);
		// 		$('#op2').html(result.employee.name);
		// 		$('#employee_id').val(result.employee.employee_id);
			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#operator').val('');
			}
		});
	}

	function confirmNgLog() {
		$('#loading').show();
		if ($('#material_description').text() == "-") {
			$('#loading').hide();
			openErrorGritter('Error!','Isi Semua Data.');
		}else{
			var material_number = $('#material_number').text();
			var material_description = $('#material_description').text();
			var part = $('#part').text();
			var material_alias = $('#material_alias').text();
			var qty_check_appearance = $('#qty_check_appearance').val();
			var qty_check_functional = $('#qty_check_functional').val();
			var qty_check_dimensional = $('#qty_check_dimensional').val();
			var final_serial_number = $('#serial_number').text();
			var lot_status = $('#lot_status').val();
			// var appearance_ok = $('#appearance_ok').val();
			// var functional_ok = $('#functional_ok').val();
			// var appearance_ng = $('#appearance_ng').val();
			// var functional_ng = $('#functional_ng').val();
			var inspector = $('#op').text();
			$('#btn_confirm').prop('disabled',true);

			
			var ng_qty = [];

			var result_check = [];
			var result_check_box_appearance = 0;
			var result_check_box_functional = 0;

			var result_counting_appearance = 0;
			var result_counting_functional = 0;

			var result_values_functional = 0;
			var result_values_dimensional = 0;

			for(var k = 0;k<point_check_id.length;k++){
				for (var j = 0; j < index_check_box_appearance; j++) {
					if (id_check_box_appearance[j] == point_check_id[k]) {
						$("input[name='condition_0_"+point_check_id[k]+"_"+j+"']:checked").each(function (i) {
				            result_check.push({point_check_id:point_check_id[k],product_index:1,result:$(this).val()});
				            result_check_box_appearance++;
				        });
					}
				}
			}
			for(var k = 0; k < point_check_id.length;k++){
				var index = 0;
				for (var l = 0; l < index_check_box_functional; l++) {
					for(var m = 1; m < parseInt($('#qty_check_fun').val())+1;m++){
						if (id_check_box_functional[index] == point_check_id[k]) {
							$("input[name='condition_"+m+"_"+point_check_id[k]+"_"+index+"']:checked").each(function (i) {
					            result_check.push({point_check_id:point_check_id[k],product_index:m,result:$(this).val()});
					            result_check_box_functional++;
					        });
						}
						index++;
					}
				}
			}


			for(var k = 0; k < point_check_id.length;k++){
				for (var l = 0; l < index_counting_appearance; l++) {
					if (id_counting_appearance[l] == point_check_id[k]) {
						result_check.push({point_check_id:point_check_id[k],product_index:1,result:$("#count_0_"+point_check_id[k]+'_'+l).text()});
						result_counting_appearance++;
					}
				}
			}

			for(var k = 0; k < point_check_id.length;k++){
				var index = 0;
				for (var l = 0; l < index_counting_functional; l++) {
					for(var m = 1; m < parseInt($('#qty_check_fun').val())+1;m++){
						if (id_counting_functional[index] == point_check_id[k]) {
				            result_check.push({point_check_id:point_check_id[k],product_index:1,result:$("#count_"+m+"_"+point_check_id[k]+'_'+index).text()});
				            result_counting_functional++;
				        }
				        index++;
					}
				}
			}
			for(var k = 0; k < point_check_id.length;k++){
				var index = 0;
				for (var l = 0; l < index_values_functional; l++) {
					for(var m = 1; m < parseInt($('#qty_check_fun').val())+1;m++){
						if (id_values_functional[index] == point_check_id[k]) {
						result_check.push({point_check_id:point_check_id[k],product_index:m,result:$("#values_"+m+"_"+point_check_id[k]+'_'+index).val()});
						}
						result_values_functional++;
						index++;
					}
				}
			}

			for(var k = 0; k < point_check_id.length;k++){
				var index = 0;
				for (var l = 0; l < index_values_dimensional; l++) {
					for(var m = 1; m < parseInt($('#qty_check_dim').val())+1;m++){
						if (id_values_dimensional[index] == point_check_id[k]) {
						result_check.push({point_check_id:point_check_id[k],product_index:m,result:$("#values_"+m+"_"+point_check_id[k]+'_'+index).val()});
						}
						result_values_dimensional++;
						index++;
					}
				}
			}

			if (result_check_box_appearance < index_check_box_appearance) {
				$('#loading').hide();
				$('#btn_confirm').removeAttr('disabled');
				openErrorGritter('Error!','Checklist Appearance Check Harus Penuh.');
				return false;
			}

			if (result_check_box_functional < index_check_box_functional) {
				$('#loading').hide();
				$('#btn_confirm').removeAttr('disabled');
				openErrorGritter('Error!','Checklist Functional Check Harus Penuh.');
				return false;
			}

			if (result_counting_appearance < index_counting_appearance) {
				$('#loading').hide();
				$('#btn_confirm').removeAttr('disabled');
				openErrorGritter('Error!','Counter Appearance Check Harus Penuh.');
				return false;
			}

			if (result_counting_functional < index_counting_functional) {
				$('#loading').hide();
				$('#btn_confirm').removeAttr('disabled');
				openErrorGritter('Error!','Counter Functional Check Harus Penuh.');
				return false;
			}

			if (result_values_functional < index_values_functional) {
				$('#loading').hide();
				$('#btn_confirm').removeAttr('disabled');
				openErrorGritter('Error!','Nilai Functional Check Harus Penuh.');
				return false;
			}

			if (result_values_dimensional < index_values_dimensional) {
				$('#loading').hide();
				$('#btn_confirm').removeAttr('disabled');
				openErrorGritter('Error!','Nilai Dimensional Check Harus Penuh');
				return false;
			}

			var data = {
				material_number:material_number,
				material_description:material_description,
				part:part,
				material_alias:material_alias,
				qty_check_appearance:qty_check_appearance,
				qty_check_functional:qty_check_functional,
				qty_check_dimensional:qty_check_dimensional,
				// appearance_ok:appearance_ok,
				// functional_ok:functional_ok,
				// appearance_ng:appearance_ng,
				// functional_ng:functional_ng,
				inspector:inspector,
				result_check:result_check,
				// serial_number:serial_number,
				final_serial_number:final_serial_number,
				lot_status:lot_status,
			}

			$.post('{{ url("index/outgoing/arisa/confirm") }}', data, function(result, status, xhr){
				if(result.status){
					cancelAll('button');
					// updateKensaCode();
					openSuccessGritter('Success!',result.message);
					$('#loading').hide();
					$('#btn_confirm').removeAttr('disabled');
				}else{
					openErrorGritter('Error!',result.message);
					$('#loading').hide();
					$('#btn_confirm').removeAttr('disabled');
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

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = day + "/" + month + "/" + year;

		return date;
	};

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
	}

	function ng_list() {
		
	}

	

	function fetchDetailRecord() {
		
	}

</script>
@endsection