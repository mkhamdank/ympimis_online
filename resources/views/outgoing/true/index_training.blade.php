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
		<div class="col-md-4" style="padding-right: 0; padding-left: 0">
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px;" border="1">
				<tbody>
					<tr>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Tanggal Produksi</th>
						<th style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Inspector</th>
					</tr>
					<tr>
						<td style="background-color: #fca311; color: #14213d; text-align: center; font-size:15px;"><input type="text" style="text-align: center;font-size: 18px" readonly name="date" id="date" class="form-control" value="{{$outgoing[0]->check_date}}"></td>
						<td style="background-color: #14213d; color: #fff; text-align: center; font-size:15px;" id="op">{{$inspector}}</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 0px;border: 0">
				<tbody>
					<tr>
						<td style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;width: 70%">
							MATERIAL
						</td>
						<th colspan="2" style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">Serial Number</th>
					</tr>
					<tr>
						<td>
							<input type="text" class="pull-right" name="material_number" style="font-size: 20px;width: 100%;text-align: center;vertical-align: middle;color: #14213d" id="material_number" readonly placeholder="Material Number" value="{{$outgoing[0]->material_number}}">
						</td>
						<td colspan="2" style="background-color: #fca311; color: #14213d; text-align: center; font-size:20px;" id="serial_number">{{$outgoing[0]->serial_number}}</td>
					</tr>
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
							Material Description
						</td>
					</tr>
					<tr>
						<td colspan="2" id="material_description" style="background-color: #fca311; text-align: center; color: #14213d; font-size: 20px;">{{$outgoing[0]->material_description}}
						</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							HISTORY DEFECT
						</td>
					</tr>
					<tr>
						<td style="background-color: #da96ff; text-align: center; color: #14213d; padding:0;font-weight: bold;width: 50%;">
							Defect Name
						</td>
						<td style="background-color: #ffe06e; text-align: center; color: #14213d; padding:0;font-weight: bold;width: 50%;">
							Qty
						</td>
					</tr>
					@foreach($outgoing as $out)
					<tr style="background-color: white">
						<td style="font-size: 20px;">
							{{$out->ng_name}}
						</td>
						<td style="font-size: 20px;">
							{{$out->ng_qty}}
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<div class="col-xs-12">
				<a class="btn btn-warning" href="{{url('index/outgoing/true')}}" style="font-size: 25px;font-weight: bold;width: 100%">
					Back
				</a>
			</div>
		</div>

		<div class="col-md-8" style="padding-right: 0;">
			<div class="row">
				<div class="col-xs-12" style="">
					<table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
				<tbody>
					<tr>
						<td colspan="2" style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;width: 50%">
							SOSIALISASI
						</td>
					</tr>
					<tr>
						<td style="background-color: #da96ff; text-align: center; color: #14213d; padding:0;font-weight: bold;width: 50%;">
							Isi Sosialisasi
						</td>
					</tr>
					<tr style="background-color: white">
						<td style="font-size: 20px;">
							<textarea style="width: 100%; height: 200px;" id="training_content" placeholder="Isi Sosialisasi"></textarea>
						</td>
					</tr>
					<tr>
						<td style="background-color: #da96ff; text-align: center; color: #14213d; padding:0;font-weight: bold;width: 50%;">
							Foto Sosialisasi
						</td>
					</tr>
					<tr style="background-color: white">
						<td style="font-size: 20px;">
							<input type="file" name="training_image" id="training_image" accept="image/*" capture="environment" onchange="readURL(this,'training_image');">
						</td>
					</tr>
					<tr>
						<td style="text-align: center;background-color: #fff"><img width="250px" id="training_image_evidence" src="" style="display: none" alt="your image" /></td>
					</tr>
				</tbody>
			</table>
				</div>
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

	});

	function cancelAll() {
		$('#training_content').val("");
		$('#training_image').val("");
		$('#training_image_evidence').html("");
		$('#training_image_evidence').hide();
	}

	function readURL(input,idfile) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
            $('#'+idfile+'_evidence').show();
              $('#'+idfile+'_evidence')
                  .attr('src', e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
      }
    }

	function confirmNgLog() {
		$('#loading').show();

		if ($('#training_image').val().replace(/C:\\fakepath\\/i, '').split(".")[0] == '' || $('#training_content').val() == '') {
			$('#loading').hide();
			openErrorGritter('Error!','Masukkan Foto & Isi Sosialisasi');
			audio_error.play();
			return false;
		}

		var formData = new FormData();
		var newAttachment  = $('#training_image').prop('files')[0];
		var file = $('#training_image').val().replace(/C:\\fakepath\\/i, '').split(".");

		formData.append('newAttachment', newAttachment);
		formData.append('training_content', $('#training_content').val());
		formData.append('serial_number', $('#serial_number').text());
		formData.append('extension', file[1]);
		formData.append('file_name', file[0]);

		$.ajax({
			url:"{{ url('index/outgoing/true/confirm/sosialisasi') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					openSuccessGritter('Success!',data.message);
					$('#training_content').val("");
					$('#training_image').val("");
					$('#training_image_evidence').html("");
					alert(data.message);
					window.location.href = "{{url('index/outgoing/true')}}";
				}else{
					openErrorGritter('Error!',data.message);
					audio_error.play();
					$('#loading').hide();
				}

			}
		});
		
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

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

	
</script>
@endsection