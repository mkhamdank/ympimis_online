<!DOCTYPE html>
<html>
<head>
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
		}
		td {
			padding: 3px;
		}
	</style>
</head>
<body>
	<div>
		<center>
			<img src="{{ url("waves.jpg") }}" style="width: 25%">
			
			@foreach($mutasi as $row)
			<p align="left">
				Berikut kami sampaikan data karyawan untuk dilakukan mutasi ke satu departemen :<br>
				Pada Tanggal : <?= $row->tanggal ?>
			</p>
			<table style="border-color: black">
				<tr style="background-color: rgb(126,86,134);">
					<th style="padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Status</th>
                        <th style="padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">NIK</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Nama</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Sub Seksi</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Seksi</th>      
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Departemen</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Jabatan</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Rekomendasi</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Ke Sub Seksi</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Ke Seksi</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Ke Departemen</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Ke Jabatan</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Alasan</th>
				</tr>
				<tr>
					<?php 
					if ( $row->status == 'All Approved') {
					 ?>
                    <td style="width: 4%; border:1px solid black; background-color: #00a65a;" align="center"><?= $row->status ?></td>
                    <?php 
                    }
                    else{
                     ?>
                    <td style="width: 4%; border:1px solid black;  background-color :#FF8C00" align="center">Aproval berada di <?= $row->status ?></td>
                    <?php 
                    }
                     ?>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->nik ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->nama ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->sub_seksi ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->seksi ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->departemen ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->jabatan ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->rekomendasi ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->ke_sub_seksi ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->ke_seksi ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->ke_departemen ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->ke_jabatan ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->alasan ?></td>
				</tr>
			</table>
			<p align="left">
				Status : <br>
			</p>
			<table style="border-color: black">
				<tr style="background-color: rgb(126,86,134);">
						<th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">6</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">5</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">4</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">3</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">2</th> 
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">1</th>
				</tr>
				<tr style="background-color: rgb(126,86,134);">
						<th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Menyetujui, Direktur HR</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Mengetahui, Presiden Direktur</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Mengetahui, Manager HRGA</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Menyetujui, GM Division</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Menyetujui, Chief/Foreman Penerima</th> 
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Dibuat Oleh Chief/Foreman Asal</th>
				</tr>
				<tr>
						<?php 
						if ( $row->direktur_hr <> 'Approved' && $row->direktur_hr <> 'Not Approved' && $row->direktur_hr <> 'Waiting') {
						 ?>
	                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_direktur_hr ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black;  background-color" align="center"><?= $row->direktur_hr ?></td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->pres_dir <> 'Approved' && $row->pres_dir <> 'Not Approved' && $row->pres_dir <> 'Waiting') {
						 ?>
	                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_pres_dir ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black;  background-color" align="center"><?= $row->pres_dir ?></td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->manager_hrga <> 'Approved' && $row->manager_hrga <> 'Not Approved' && $row->manager_hrga <> 'Waiting') {
						 ?>
	                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_manager ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black;  background-color" align="center"><?= $row->manager_hrga ?></td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->gm_division <> 'Approved' && $row->gm_division <> 'Not Approved' && $row->gm_division <> 'Waiting') {
						 ?>
	                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_gm ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black;  background-color" align="center"><?= $row->gm_division ?></td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->chief_or_foreman_tujuan <> 'Approved' && $row->chief_or_foreman_tujuan <> 'Not Approved' && $row->chief_or_foreman_tujuan <> 'Waiting') {
						 ?>
	                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_atasan_tujuan ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black;  background-color" align="center"><?= $row->chief_or_foreman_tujuan ?></td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->chief_or_foreman_asal <> 'Approved' && $row->chief_or_foreman_asal <> 'Not Approved' && $row->chief_or_foreman_asal <> 'Waiting') {
						 ?>
	                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_atasan_asal ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black;" align="center"><?= $row->chief_or_foreman_asal ?></td>
	                    <?php 
	                    }
	                     ?>
				</tr>
				<tr>
						<?php 
						if ( $row->date_direktur_hr != null) {
						 ?>
	                    <td style="width: 4%; border:1px solid black; ; background-color:#00a65a" align="center"><?= $row->date_direktur_hr ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black; background-color:red;" align="center">Belum Diproses</td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->date_pres_dir != null) {
						 ?>
	                    <td style="width: 4%; border:1px solid black; ; background-color:#00a65a" align="center"><?= $row->date_pres_dir ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black; background-color:red;" align="center">Belum Diproses</td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->date_manager_hrga != null) {
						 ?>
	                    <td style="width: 4%; border:1px solid black; ; background-color:#00a65a" align="center"><?= $row->date_manager_hrga ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black; background-color:red;" align="center">Belum Diproses</td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->date_gm != null) {
						 ?>
	                    <td style="width: 4%; border:1px solid black; ; background-color:#00a65a" align="center"><?= $row->date_gm ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black; background-color:red;" align="center">Belum Diproses</td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->date_atasan_tujuan != null) {
						 ?>
	                    <td style="width: 4%; border:1px solid black; ; background-color:#00a65a" align="center"><?= $row->date_atasan_tujuan ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black; background-color:red;" align="center">Belum Diproses</td>
	                    <?php 
	                    }
	                     ?>
						<?php 
						if ( $row->date_atasan_asal != null) {
						 ?>
	                    <td style="width: 4%; border:1px solid black; ; background-color:#00a65a" align="center"><?= $row->date_atasan_asal ?></td>
	                    <?php 
	                    }
	                    else{
	                     ?>
	                    <td style="width: 4%; border:1px solid black; background-color:red;" align="center">Belum Diproses</td>
	                    <?php 
	                    }
	                     ?>
				</tr>
			</table>
			<br><br>
			<table align="center">
				<tr>
					<th style="background-color: #FFFF00;color:black;">Note</th>
				</tr>
				<tr>
					<td>Urutan Persetujuan : 1, 2, 3, 4, 5, 6</td>
				</tr>
			</table>			
			@endforeach
		</center>
	</div>
</body>
</html>