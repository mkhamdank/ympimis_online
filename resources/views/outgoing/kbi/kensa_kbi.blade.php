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
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Date</th>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Inspector</th>
					</tr>
					<tr>
						<td style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px;" id="date">{{date("Y-m-d")}}</td>
						<td style="background-color: #14213d; color: #fff; text-align: center; font-size:15px;" id="op">{{$inspector}}</td>
					</tr>
					<tr>
						<th colspan="2" style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Serial Number</th>
					</tr>
					<tr>
						<td colspan="2" style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px;"><input type="text" id="serial_number" placeholder="Scan Serial Number Here . . ." style="width: 100%;text-align: center;font-size: 20px;padding: 5px"></td>
					</tr>
					
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 14px;font-weight: bold;">
							PART NO.
						</td>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 14px;font-weight: bold;">
							PART NAME
						</td>
					</tr>
					<tr>
						<td id="material_number" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 20px;">-
						</td>
						<td id="material_description" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 20px;">-
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							QTY
						</td>
					</tr>
					<tr>
						<!-- <td>
							<input type="number" class="pull-right numpad2" name="qty_rec" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_rec" placeholder="Quantity Recieve">
						</td> -->
						<td>
							<input type="number" class="pull-right" name="qty_check" style="height: 40px;font-size: 1.5vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_check" placeholder="Quantity Check" onchange="checkQty(this.value);" readonly="">
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

<!-- <div class="modal fade" id="modalOperator" data-backdrop="static">
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
</div> -->

<div class="modal fade" id="modalNg">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding">
					<h4 id="ng_name" style="width: 100%;background-color: #fca311;font-size: 25px;font-weight: bold;padding: 5px;text-align: center;color: #14213d"></h4>
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
						<tbody>
							<tr>
								<td style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									QTY
								</td>
								<td style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									Status
								</td>
							</tr>
							<tr>
								<td>
									<input type="number" class="pull-right numpad" name="qty_ng" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="qty_ng" placeholder="Qty NG">
								</td>
								<td>
									<select name="status_ng" style="height: 50px;font-size: 2vw;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="status_ng" data-placeholder="Status NG">
										<option value="-">Pilih Status NG</option>
										<option value="Repair">Repair</option>
										<option value="Scrap">Scrap</option>
										<option value="Return">Return</option>
									</select>
								</td>
							</tr>
							<tr>
								<td colspan="2" style="background-color: #14213d; text-align: center; color: #fff; padding:0;font-size: 20px;font-weight: bold;">
									Note
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<textarea type="text" class="pull-right" name="note_ng" style="height: 50px;font-size: 20px;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="note_ng" placeholder="Note"></textarea>
								</td>
							</tr>
						</tbody>
					</table>

					<div style="padding-top: 10px">
						<button id="confNg" style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white;" onclick="confNgTemp()" class="btn btn-success">CONFIRM</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="record-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<center style="background-color: #ffc654;padding: 5px"><h4 class="modal-title" id="myModalLabel" style="font-weight: bold;"></h4></center>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-3 col-md-offset-3">
						<span style="font-weight: bold;">Date From</span>
						<div class="form-group">
							<div class="input-group date">
								<div class="input-group-addon bg-white">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<span style="font-weight: bold;">Date To</span>
						<div class="form-group">
							<div class="input-group date">
								<div class="input-group-addon bg-white">
									<i class="fa fa-calendar"></i>
								</div>
								<input type="text" class="form-control datepicker" id="date_to"name="date_to" placeholder="Select Date To" autocomplete="off">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 col-md-offset-3">
						<span style="font-weight: bold;">Vendor</span>
						<div class="form-group">
							
							<input type="text" name="vendor" id="vendor_choose" style="color: black !important" hidden>
						</div>
					</div>
					<div class="col-md-3">
						<span style="font-weight: bold;">Material</span>
						
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-md-offset-2">
						<div class="col-md-10">
							<div class="form-group pull-right">
								<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
								<button class="btn btn-primary col-sm-14" onclick="fetchDetailRecord()">Search</button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12" style="overflow-x: scroll;">
					<table class="table table-bordered" id="tableDetail">
						<thead style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
				            <tr>
				                <th style="font-weight: bold;" rowspan="2">#</th>
				                <th style="font-weight: bold;" rowspan="2">Loc</th>
				                <th style="font-weight: bold;" rowspan="2">Lot Number</th>
				                <th style="font-weight: bold;" rowspan="2">Date</th>
				                <th style="font-weight: bold;" rowspan="2">Inspector</th>
				                <th style="font-weight: bold;" rowspan="2">Vendor</th>
				                <th style="font-weight: bold;" rowspan="2">Invoice</th>
				                <th style="font-weight: bold;" rowspan="2">Inspection Level</th>
				                <th style="font-weight: bold;" rowspan="2">Material</th>
				                <th style="font-weight: bold;" rowspan="2">Desc</th>
				                <th style="font-weight: bold;" rowspan="2">Qty Rec</th>
				                <th style="font-weight: bold;" rowspan="2">Qty Check</th>
				                <th style="font-weight: bold;" rowspan="2">Defect</th>
				                <th style="font-weight: bold;" colspan="3">Jumlah NG</th>
				                <th style="font-weight: bold;" rowspan="2">Note</th>
				                <th style="font-weight: bold;" rowspan="2">NG Ratio</th>
				            </tr>
				            <tr>
				                <th style="font-weight: bold;">Repair</th>
				                <th style="font-weight: bold;">Return</th>
				                <th style="font-weight: bold;">Scrap</th>
				            </tr>
				        </thead>
						<tbody id="bodyTableDetail">
							
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				
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
		ng_list();
	});

	function cancelAll() {
		$('#material_number').html('-');
		$('#material_description').html('-');
		$('#qty_check').val('');
		$('#total_ok').val('0');
		$('#total_ng').val('0');
		$('#ng_ratio').val('0');
		var jumlah_ng = '{{$nomor+1}}';
		for (var i = 1; i <= jumlah_ng; i++ ) {
			$('#count'+i).html(0);
		}
		$('#serial_number').removeAttr('disabled');
		$('#serial_number').val('');
		$('#serial_number').focus();
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	$('#serial_number').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			var data = {
				serial_number:$('#serial_number').val()
			}
			$.get('{{ url("scan/kensa/kbi") }}', data, function(result, status, xhr){
				if(result.status){
					openSuccessGritter('Success','Scan Serial Number Success');
					$("#serial_number").prop('disabled',true);
					$('#material_number').html(result.serial_number.material_number);
					$('#material_description').html(result.serial_number.part_name);
					$('#qty_check').val(result.serial_number.qty);
					$('#total_ok').val(result.serial_number.qty);
				}else{
					openErrorGritter('Error!','Serial Number Invalid');
					audio_error.play();
					$("#serial_number").val('');
					$("#serial_number").focus();
				}
			});
		}
	});

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
			var material_number = $('#material_number').text();
			var material_description = $('#material_description').text();
			var qty_check = $('#qty_check').val();
			var total_ok = $('#total_ok').val();
			var total_ng = $('#total_ng').val();
			var ng_ratio = $('#ng_ratio').val();
			var serial_number = $('#serial_number').val();
			var inspector = $('#op').text();
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
			}

			$.post('{{ url("index/kensa/kbi/confirm") }}', data, function(result, status, xhr){
				if(result.status){
					cancelAll();
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