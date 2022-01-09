@extends('layouts.display')
@section('stylesheets')
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
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	.dataTable > thead > tr > th[class*="sort"]:after{
		content: "" !important;
	}
	#queueTable.dataTable {
		margin-top: 0px!important;
	}
	.color {
		width: 50px;
		height: 50px;
		-webkit-animation: blinks 1s infinite;  /* Safari 4+ */
		-moz-animation: blinks 1s infinite;  /* Fx 5+ */
		-o-animation: blinks 1s infinite;  /* Opera 12+ */
		animation: blinks 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes blinks {
		0%, 49% {
			background-color: #fffcb7;
		}
		50%, 100% {
			background-color: rgb(255,100,120);
		}
	}

	.color2 {
		width: 50px;
		height: 50px;
		-webkit-animation: sukses 1s infinite;  /* Safari 4+ */
		-moz-animation: sukses 1s infinite;  /* Fx 5+ */
		-o-animation: sukses 1s infinite;  /* Opera 12+ */
		animation: sukses 1s infinite;  /* IE 10+, Fx 29+ */
	}

	@-webkit-keyframes sukses {
		0%, 49% {
			background-color: #fffcb7;
		}
		50%, 100% {
			background-color: rgb(100,250,120);
		}
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">
	<h1>
		
	</h1>
</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-left: 0px; padding-right: 0px;">
	<div class="row">
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		// fetchTable();
		// setInterval(fetchTable, 1000);
		// setInterval(getWhatsapp,20000);
		getWhatsapp();
		// var url = 'https://api.chat-api.com/instance150276/messages?token=owl5cvgsqlil60xf';
		// $.get(url, function (data) { // Make a GET request
		//     // for (var i = 0; i < data.messages.length; i++) { // For each message
		//     //     var message = data.messages[i];
		//     //     // console.log(message.author + ': ' + message.body); //Send it to console
		//     // }
		//     console.log(data);
		// });
	});

	function getWhatsapp() {
		var pesan = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
		var url = '{{url("kirimTelegram")}}' + '/' + pesan;
		$.get(url, function(result, status, xhr) { // Make a GET request
		    if(result.status){
		    	// console.log(result.status);
		    }else{
		    	// console.log(result.status);
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
</script>
@endsection

