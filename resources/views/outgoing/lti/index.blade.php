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
		height:454px;
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
	.datepicker-days > table > thead,
	.datepicker-days > table > thead >tr>th,
    .datepicker-months > table > thead>tr>th,
    .datepicker-years > table > thead>tr>th,
    .datepicker-decades > table > thead>tr>th,
    .datepicker-centuries > table > thead>tr>th{
        background-color: white;
        color: #696969 !important;
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
		<div class="col-md-6" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
				<tbody>
					<tr>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Tanggal Produksi</th>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Inspector</th>
					</tr>
					<tr>
						<td style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px;"><input type="text" style="text-align: center;font-size: 18px" readonly name="date" id="date" class="form-control datepicker" value="{{date('Y-m-d')}}"></td>
						<td style="background-color: #14213d; color: #fff; text-align: center; font-size:15px;" id="op">{{$inspector}}</td>
					</tr>
					<!-- <tr>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Loc</th>
						<th colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Urutan Lot Dalam Satu Kedatangan (Bukan Qty Recieve)</th>
					</tr> -->
					<!-- <tr>
						
						<td colspan="2">
							<input type="text" class="pull-right numpad2" name="lot_number" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="lot_number" placeholder="Urutan Lot Dalam Satu Kedatangan">
						</td>
					</tr> -->
					
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;width: 70%">
							MATERIAL
						</td>
						<th colspan="2" style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Serial Number</th>
					</tr>
					<tr>
						<td>
							<select class="form-control select2" name="material_number" id='material_number' data-placeholder="Select Material Number" style="width: 100%;font-size: 20px" onchange="selectMaterial(this.value);">
								<option value="">Select Material</option>
								@foreach($materials as $material)
								<option value="{{ $material->material_number }} - {{ $material->material_description }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
								@endforeach
							</select>
							<!-- <input type="text" class="pull-right" name="material_number" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="material_number" placeholder="Material Number" onkeyup="checkMaterial(this.value)"> -->
						</td>
						<td colspan="2" style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px;" id="serial_number"></td>
					</tr>
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							Material Description
						</td>
						<!-- <td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							Target Bulan Ini
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							Sisa Target
						</td> -->
					</tr>
					<tr>
						<td colspan="2" id="material_description" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 20px;">-
						</td>
						<!-- <td id="target" style="background-color: #14213d; text-align: center; color: #fff; font-size: 20px;">
						</td>
						<td id="sisa_target" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 20px;">
						</td> -->
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;">
							QTY
						</td>
					</tr>
					<tr>
						<!-- <td>
							<input type="number" class="pull-right numpad2" name="qty_rec" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_rec" placeholder="Quantity Recieve">
						</td> -->
						<td>
							<input type="number" class="pull-right numpad" name="qty_check" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check" placeholder="Quantity Check" onchange="checkQty(this.value);">
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<!-- <tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							INVOICE NUMBER
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							INSPECTION LEVEL
						</td>
					</tr> -->
					<!-- <tr>
						<td>
							<input type="text" class="pull-right" name="invoice" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="invoice" placeholder="Invoice">
						</td>
						<td>
							
						</td>
					</tr> -->
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							RESULT
						</td>
					</tr>
					<tr>
						<!-- <td style="background-color: #80e5ff; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							REPAIR
						</td> -->
						<td style="background-color: #da96ff; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							QTY OK
						</td>
						<td style="background-color: #ffe06e; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							QTY NG
						</td>
					</tr>
					<tr>
						<!-- <td>
							<input type="text" class="pull-right" name="repair" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="repair" placeholder="Qty Repair" readonly value="0">
						</td> -->
						<td>
							<input type="text" class="pull-right" name="total_ok" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="total_ok" placeholder="Qty OK" readonly value="0">
						</td>
						<td>
							<input type="text" class="pull-right" name="total_ng" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="total_ng" placeholder="Qty NG" readonly value="0">
						</td>
					</tr>
					<tr>
						<td colspan="2" style="background-color: #ff8c8c; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							NG RATIO (%)
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="text" class="pull-right" name="ng_ratio" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="ng_ratio" placeholder="NG Ratio (%)" readonly value="0">
						</td>
					</tr>
					<!-- <tr>
						<td style="background-color: #ffe06e; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							RETURN
						</td>
						<td style="background-color: #ffcd9c; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							STATUS LOT
						</td>
					</tr> -->
					<!-- <tr>
						<td>
							<input type="text" class="pull-right" name="return" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="return" placeholder="Qty Return" readonly value="0">
						</td>
						<td>
							<select name="status_lot" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="status_lot" data-placeholder="Status Lot">
								<option value="-">Pilih Status Lot</option>
								<option value="Lot OK">Lot OK</option>
								<option value="Lot Out">Lot Out</option>
							</select>
						</td>
					</tr> -->
				</tbody>
			</table>
		</div>

		<div class="col-md-6" style="padding-right: 0;">
			<!-- <div class="row" style="padding-left: 15px;padding-right: 15px">
				<button class="btn btn-info pull-right" onclick="fetchDetailRecord()" style="width: 100%;font-weight: bold;font-size: 20px">Record</button>
			</div> -->
			<div id="ngList2">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1" id="tableNgList">
					<thead>
						<tr>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >NG Name</th>
							<th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >#</th>
							<th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;" >Count</th>
						</tr>
					</thead>
					<tbody id="bodyTableNgList">
						<?php $no = 1; ?>
						<input type="hidden" name="ng_list_count" id="ng_list_coung" value="{{count($ng_lists)}}">
						@foreach($ng_lists as $nomor => $ng_list)
						<?php if ($no % 2 === 0 ) {
							$color = 'style="background-color: #fffcb7"';
						} else {
							$color = 'style="background-color: #ffd8b7"';
						}
						?>
						<input type="hidden" id="loop" value="{{$loop->count}}">
						<tr <?php echo $color ?>>
							<td id="minus" onclick="minus({{$nomor+1}})" style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">-</td>
							<td id="ng{{$nomor+1}}" style="font-size: 20px;">{{ $ng_list->ng_name }}</td>
							<td id="plus" onclick="plus({{$nomor+1}})" style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;" class="unselectable">+</td>
							<td style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;"><span id="count{{$nomor+1}}">0</span></td>
						</tr>
						<?php $no+=1; ?>
						@endforeach
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
					<button class="btn btn-danger" id="btn_cancel" onclick="cancelAll()" style="font-size: 25px;font-weight: bold;width: 100%">
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

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 30%;align-items:center"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:1.5vw; height: 50px;width:100%"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-primary" style="font-size:1.5vw; width:40px;background-color:#b5ffa8;color:black"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:1.5vw; width: 100%;background-color:#ffb84d;color:black"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

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
		cancelAll();

		$('.select2').select2({
			minimumInputLength: 3
		});

		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		$('.numpad2').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});

		updateKensaCode();
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
		ng_list();
	});

	function updateKensaCode() {
		$.get('{{ url("fetch/kensa/serial_number/lti") }}',  function(result, status, xhr){
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

	function cancelAll() {
		$('#material_number').val('').trigger('change.select2');
		$('#material_description').html('-');
		$('#qty_check').val('');
		$('#total_ok').val('0');
		$('#total_ng').val('0');
		$('#ng_ratio').val('0');
		// $('#target').html('');
		// $('#sisa_target').html('');
		// $('#serial_number').html('');
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			// for (var j = 0; j < ng_name.length; j++ ) {
				$('#count'+i).html(0);
			// }
		}
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	// $('#operator').keydown(function(event) {
	// 	if (event.keyCode == 13 || event.keyCode == 9) {
	// 		// if($("#operator").val().length >= 8){
	// 			// var data = {
	// 			// 	employee_id : $("#operator").val(),
	// 			// }
				
	// 			// $.get('{{ url("scan/injeksi/operator") }}', data, function(result, status, xhr){
	// 			// 	if(result.status){
	// 					$('#modalOperator').modal('hide');
	// 			// 		$('#op').html(result.employee.employee_id);
	// 			// 		$('#op2').html(result.employee.name);
	// 			// 		$('#employee_id').val(result.employee.employee_id);
	// 			// 	}
	// 			// 	else{
	// 			// 		audio_error.play();
	// 			// 		openErrorGritter('Error', result.message);
	// 			// 		$('#operator').val('');
	// 			// 	}
	// 			// });
	// 			$('#op').html($('#operator').val());
	// 		// }
	// 		// else{
	// 		// 	// openErrorGritter('Error!', 'Employee ID Invalid.');
	// 		// 	audio_error.play();
	// 		// 	$("#operator").val("");
	// 		// }			
	// 	}
	// });

	function selectMaterial(material) {
		var material_number = material.split(' - ')[0];
		var material_description = material.split(' - ')[1];
		$('#material_description').html(material_description);
		// var data = {
		// 	material_number:material_number,
		// 	periode:$("#date").val()
		// }

		// $.get('{{ url("fetch/outgoing/true/material") }}', data, function(result, status, xhr){
		// 	if(result.status){
		// 		// $('#target').html(result.target.qty);
		// 		// $('#sisa_target').html(parseInt(result.target.qty)-parseInt(result.target.qty_actual));
		// 		// $('#serial_number').html(result.target.serial_number);
		// 	}else{
		// 	}
		// });
	}

	function showModalNg(ng_name) {
		
	}

	function confNgTemp() {
		
	}

	function fetchNgTemp() {
		
	}

	function deleteNgTemp(id) {
		
	}

	function checkQty(value) {
		$('#total_ok').val(value);
		var qty_ng = 0;
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			if ($('#count'+i).text() != 0) {
				qty_ng = qty_ng + parseInt($('#count'+i).text());
			}
		}
		var total_ok = value - qty_ng;
		$('#total_ok').val(total_ok);
		$('#total_ng').val(qty_ng);
		$('#ng_ratio').val(((qty_ng/value)*100).toFixed(1));
	}

	function plus(id){
		var count = $('#count'+id).text();
		if ($('#material_description').text() == "-" || $('#qty_check').val() == "") {
			openErrorGritter('Error!','Isi Semua Data.');
		}else{
			$('#total_ok').val(parseInt($('#total_ok').val())-1);
			$('#total_ng').val(parseInt($('#total_ng').val())+1);
			$('#ng_ratio').val(((parseInt($('#total_ng').val())/parseInt($('#qty_check').val()))*100).toFixed(1));
			$('#count'+id).text(parseInt(count)+1);
		}
	}

	function minus(id){
		var count = $('#count'+id).text();
		if ($('#material_description').text() == "-" || $('#qty_check').val() == "") {
			openErrorGritter('Error!','Isi Semua Data.');
		}else{
			if(count > 0)
			{
				$('#total_ok').val(parseInt($('#total_ok').val())+1);
				$('#total_ng').val(parseInt($('#total_ng').val())-1);
				$('#ng_ratio').val(((parseInt($('#total_ng').val())/parseInt($('#qty_check').val()))*100).toFixed(1));
				$('#count'+id).text(parseInt(count)-1);
			}
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

	function confirmNgLog() {
		$('#loading').show();
		if ($('#material_description').text() == "-" || $('#qty_check').val() == "") {
			$('#loading').hide();
			openErrorGritter('Error!','Isi Semua Data.');
		}else{
			var material_number = $('#material_number').val().split(' - ')[0];
			var material_description = $('#material_number').val().split(' - ')[1];
			var qty_check = $('#qty_check').val();
			var total_ok = $('#total_ok').val();
			var total_ng = $('#total_ng').val();
			var ng_ratio = $('#ng_ratio').val();
			var inspector = $('#op').text();
			var date = $('#date').val();
			var serial_number = $('#serial_number').text();
			$('#btn_confirm').prop('disabled',true);

			var ng_name = [];
			var ng_qty = [];
			var jumlah_ng = '{{$nomor+1}}';
			for (var i = 1; i <= jumlah_ng; i++ ) {
				if ($('#count'+i).text() != 0) {
					ng_name.push($('#ng'+i).text());
					ng_qty.push($('#count'+i).text());
				}
			}

			var data = {
				material_number:material_number,
				material_description:material_description,
				qty_check:qty_check,
				total_ok:total_ok,
				total_ng:total_ng,
				serial_number:serial_number,
				ng_ratio:ng_ratio,
				inspector:inspector,
				ng_name:ng_name,
				ng_qty:ng_qty,
				jumlah_ng:jumlah_ng,
				check_date:date,
			}

			$.post('{{ url("index/outgoing/lti/confirm") }}', data, function(result, status, xhr){
				if(result.status){
					cancelAll();
					updateKensaCode();
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

	function fetchDetailRecord() {
		
	}

</script>
@endsection