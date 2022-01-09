@extends('layouts.display')
@section('stylesheets')
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/dataTables.bootstrap4.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">
<style type="text/css">
	input {
		line-height: 22px;
	}
	thead>tr>th{
		text-align:center;
	}
	tbody>tr>td{
		text-align:center;
		color: black;
	}
	tfoot>tr>th{
		text-align:center;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
	}

	.content-wrapper{
		color: white;
		font-weight: bold;
		background-color: #313132 !important;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

	.gambar {
	    width: 180px;
	    background-color: none;
	    border-radius: 5px;
	    margin-left: 15px;
	    margin-top: 15px;
	    display: inline-block;
	    border: 2px solid white;
	  }


	#loading, #error { display: none; }
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	.bootstrap-datetimepicker-widget { background-color: #fff !important; }

	.datepicker-days > table > thead,
	.datepicker-months > table > thead,
	.datepicker-years > table > thead,
	.datepicker-decades > table > thead,
	.datepicker-centuries > table > thead{
		background-color: white
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	.page-wrapper{
		padding-top: 0px;
	}

	.dataTables_filter {
		color: white
	}

	.dataTables_info{
		color: white
	}
	
</style>
@stop
@section('header')
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid" style="padding-top: 10px;padding-left: 10px;padding-right: 10px">
	<div class="row" style="text-align: center;margin-left: 5px;margin-right: 5px">
		<!-- <div class="col-md-12" style="margin-left: 0px;margin-right: 0px;padding-bottom: 10px;padding-left: 0px"> -->
			<div class="col-md-3" style="background-color: rgb(126,86,134);padding-left: 5px;padding-right: 5px;height:36px;vertical-align: middle;">
				<span style="font-size: 20px;color: white;width: 100%;" id="periode"></span>
			</div>
			<div class="col-md-2" style="padding-left: 5px;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #469937; color: white;">
						<i class="fa fa-calendar" style="padding: 10px"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From">
				</div>
			</div>
			<div class="col-md-2" style="padding-left: 0;">
				<div class="input-group date">
					<div class="input-group-addon bg-green" style="border: none; background-color: #469937; color: white;">
						<i class="fa fa-calendar" style="padding: 10px"></i>
					</div>
					<input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Select Date To">
				</div>
			</div>
			<div class="col-md-2" style="padding-left: 0;">
				<div class="form-group">
					<select class="form-control select3" multiple="multiple" id='materialSelect' data-placeholder="Select Material" style="width: 100%;color: black !important" onchange="changeMaterial()">
						@foreach($materials as $material)
						<option value="{{$material->material_number}}">{{$material->material_number}} - {{$material->material_description}}</option>
						@endforeach
					</select>
					<input type="text" name="material" id="material" style="color: black !important" hidden>
				</div>
			</div>
			<div class="col-md-1" style="padding-left: 0;">
				<button class="btn btn-success pull-left" onclick="fetchLotStatus()" style="font-weight: bold;">
					Search
				</button>
			</div>
			<div class="col-md-2" style="padding-left: 0;">
				<div class="pull-right" id="last_update" style="margin: 0px;padding-top: 0px;padding-right: 0px;color: white"></div>
			</div>
		<!-- </div> -->
		<div class="col-md-12" id="lot_status">
			
		</div>
		<div class="col-md-12" style="padding-left: 0px;padding-right: 0px;padding-top: 10px">
			<table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 15px">
				<thead style="background-color: rgb(126,86,134);">
					<tr>
						<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px;color: white; width: 1%">QC SN</th>
						<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px;color: white; width: 1%">Production SN</th>
						<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px;color: white; width: 3%">Material</th>
						<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px;color: white; width: 2%">PIC</th>
						<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px;color: white; width: 1%">Status</th>
						<th style="border: 1px solid black; font-size: 1vw; padding-top: 2px; padding-bottom: 2px;color: white; width: 2%">At</th>
					</tr>
				</thead>
				<tbody id="body_table_lot" style="text-align:center;">
					
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="{{ url("js/jquery.dataTables.min.js") }}"></script>
<script src="{{ url("js/dataTables.bootstrap4.min.js") }}"></script>
<script src="{{ url("js/jquery.flot.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		$('.select2').select2();
		fetchLotStatus();
		// setInterval(fetchLotStatus, 300000);
		$('.select3').select2({
			
		});
	});

	function changeMaterial() {
		$("#material").val($("#materialSelect").val());
	}

	$('.datepicker').datepicker({
		<?php $tgl_max = date('Y-m-d') ?>
		autoclose: true,
		format: "yyyy-mm-dd",
		todayHighlight: true,	
		endDate: '<?php echo $tgl_max ?>'
	});

	function fetchLotStatus() {
		$('#last_update').html('<p><i class="fa fa-fw fa-clock-o"></i> Last Updated: '+ getActualFullDate() +'</p>');

		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			material:$('#material').val(),
		}
		$.get('{{ url("fetch/outgoing/lot_status/arisa") }}',data, function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){
					var lot_statuses = '';
					$('#lot_status').html('');
					$.each(result.lot_status, function(key,value){
						if (parseInt(value.count_ok) > 0) {
							var bgcolor_ok = 'rgb(0, 166, 90)';
							var color_ok = 'white';
						}else{
							var bgcolor_ok = 'white';
							var color_ok = 'black';
						}

						if (parseInt(value.count_out) > 0) {
							var bgcolor_out = '#dd4b39';
							var color_out = 'white';
						}else{
							var bgcolor_out = 'white';
							var color_out = 'black';
						}
						lot_statuses += '<div class="gambar" style="margin-top:0px">';
							lot_statuses += '<table style="text-align:center;width:100%">';
								lot_statuses += '<tr>';
									lot_statuses += '<td colspan="2" style="border: 1px solid #fff !important;background-color: white;color: black;font-size: 20px">';
									lot_statuses += value.part_category;
									lot_statuses += '</td>';
								lot_statuses += '</tr>';
								lot_statuses += '<tr>';
									lot_statuses += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;">LOT OK';
									lot_statuses += '</td>';
									lot_statuses += '<td style="border: 1px solid #fff;border-bottom: 2px solid white;background-color: black;color: white;font-size: 15px;width: 50%;">LOT OUT';
									lot_statuses += '</td>';
								lot_statuses += '</tr>';
								lot_statuses += '<tr>';
									lot_statuses += '<td style="border: 1px solid #fff;color: '+color_ok+';background-color:'+bgcolor_ok+';font-size: 80px;"><span>'+value.count_ok+'</span>';
									lot_statuses += '</td>';
									lot_statuses += '<td style="border: 1px solid #fff;font-size: 80px;color: '+color_out+';background-color:'+bgcolor_out+';"><span>'+value.count_out+'</span>';
									lot_statuses += '</td>';
								lot_statuses += '</tr>';
							lot_statuses += '</table>';
						lot_statuses += '</div>';
					});

					$('#lot_status').append(lot_statuses);

					$('#table_lot').DataTable().clear();
					$('#table_lot').DataTable().destroy();
					$('#body_table_lot').html("");
					var body_lot = "";

					$.each(result.lot_resume, function(key2,value2){
						if (value2.lot_status == 'LOT OUT') {
							var color = '#ffadad';
						}else{
							var color = '#fff';
						}

						body_lot += '<tr>';
						body_lot += '<td style="background-color:'+color+';font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.final_serial_number+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.serial_number+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.material_number+' - '+value2.material_description+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.inspector.replace(/(.{14})..+/, "$1&hellip;")+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.lot_status+'</td>';
						body_lot += '<td style="background-color:'+color+';font-size: 1vw; padding-top: 2px; padding-bottom: 2px;">'+value2.created+'</td>';
						body_lot += '</tr>';
					});

					$('#body_table_lot').append(body_lot);

					var table = $('#table_lot').DataTable({
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
						'searching': true	,
						'ordering': true,
						'order': [],
						'info': true,
						'autoWidth': true,
						"sPaginationType": "full_numbers",
						"bJQueryUI": true,
						"bAutoWidth": false,
						"processing": true
					});

					// 	if (value2.location == 'wi1') {
				 //  			var loc = 'WI 1';
				 //  		}else if (value2.location == 'wi2') {
				 //  			var loc = 'WI 2';
				 //  		}else if(value2.location == 'ei'){
				 //  			var loc = 'EI';
				 //  		}else if(value2.location == 'sx'){
				 //  			var loc = 'Sax Body';
				 //  		}else if (value2.location == 'cs'){
				 //  			var loc = 'Case';
				 //  		}else if(value2.location == 'ps'){
				 //  			var loc = 'Pipe Silver';
				 //  		}
					

					$('#periode').html('Periode '+result.monthTitle);
				}
			}
		});
	}

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

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year;
	}


</script>
@endsection