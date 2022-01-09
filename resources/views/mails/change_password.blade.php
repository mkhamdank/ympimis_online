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
	</style>
</head>
<body>
	<div>
		<center>
			<img src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('mirai.jpg')))}}" alt=""><br>
			<p style="font-size: 18px;">Change Password Information (パスワード変更の情報)</p>
			This is an automatic notification. Please do not reply to this address.
			<p>Your password has been changed on {{date('d F Y H:i:s')}}.<br>If that's you, you can safely ignore this email.<br>If not you, please contact Administrator at the contact below.</p>
			<!-- <table style="border:1px solid black; border-collapse: collapse;" width="50%">
				<thead>
					<tr>
						<th style="width: 1%; border:1px solid black;">
							<a href="{{url('')}}">LOGIN</a>
						</th>
					</tr>
				</thead>
			</table> -->
			<p>
				<b>Thanks & Regards,</b>
			</p>
			<p>PT. Yamaha Musical Products Indonesia<br>
				Jl. Rembang Industri I / 36<br>
				Kawasan Industri PIER - Pasuruan<br>
				Phone   : 0343 – 740290 (Ext. 1189)<br>
				Fax.    : 0343 - 740291
			</p>
		</center>
	</div>
</body>
</html>