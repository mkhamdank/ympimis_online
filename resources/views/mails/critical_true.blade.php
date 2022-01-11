<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		td{
			padding-right: 5px;
			padding-left: 5px;
			padding-top: 0px;
			padding-bottom: 0px;
		}
		th{
			padding-right: 5px;
			padding-left: 5px;			
		}
		.button {
		  background-color: #4CAF50; /* Green */
		  border: none;
		  color: white;
		  padding: 10px;
		  text-align: center;
		  text-decoration: none;
		  display: inline-block;
		  font-size: 16px;
		  margin: 4px 2px;
		  cursor: pointer;
		  border-radius: 4px;
		  cursor: pointer;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img style="width: 24px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('yamaha_putih.png')))}}" alt=""><br><br>
			<span style="font-weight: bold; color: purple; font-size: 24px;">Critical Defect PT. TRUE INDONESIA</span><br>
			<p style="color: black">This is an automatic notification. Please do not reply to this address.</p>
		</center>
	</div>
	<div>
		<center>
				<table style="border:1px solid black; border-collapse: collapse;" width="60%">
					<thead style="text-align: center;">
						<tr>
							<th colspan="2" style="border:1px solid black;font-weight: bold;background-color: #d4e157;color: black">Details</th>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Serial Number</th>
							<td style="border:1px solid black;text-align: left;">
								{{$data['serial_number']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Material</th>
							<td style="border:1px solid black;text-align: left;">
								{{$data['material_number']}} - {{$data['material_description']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Qty Check</th>
							<td style="border:1px solid black;text-align: right;">
								{{$data['qty_check']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Total OK</th>
							<td style="border:1px solid black;text-align: right;">
								{{$data['total_ok']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Total Defect</th>
							<td style="border:1px solid black;text-align: right;">
								{{$data['total_ng']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Defect Ratio</th>
							<td style="border:1px solid black;text-align: right;">
								{{$data['ng_ratio']}} %
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Defect</th>
							<td style="border:1px solid black;text-align: left">
								{{$data['ng_name']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Qty Defect</th>
							<td style="border:1px solid black;text-align: right;">
								{{$data['ng_qty']}}
							</td>
						</tr>
						<tr>
							<th style="border:1px solid black;font-weight: bold;background-color: rgb(126,86,134);text-align: left;color: white;">Inspector</th>
							<td style="border:1px solid black;text-align: left">
								{{$data['inspector']}}
							</td>
						</tr>
						
					</thead>
					<!-- <tbody style="text-align: center;">
					</tbody> -->
				</table>
				<br>
				<!-- <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i> &#8650;</span><br> -->
					<!-- <a class="button" href="{{url('index/qa/audit_ng_jelas/handling/')}}">Input Penanganan</a> -->
				<br>
				<br>
				<p>
					<b>Thanks & Regards,</b>
				</p>
				<p>PT. Yamaha Musical Products Indonesia<br>
					Jl. Rembang Industri I / 36<br>
					Kawasan Industri PIER - Pasuruan<br>
					Phone   : 0343 – 740290<br>
					Fax.    : 0343 - 740291
				</p>
		</center>
	</div>
</body>
</html>