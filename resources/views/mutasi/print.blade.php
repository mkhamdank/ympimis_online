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
				Pada Tanggal : <?= $row->mutasi_tanggal ?>
			</p>
			<table style="border-color: black">
				<tr style="background-color: rgb(126,86,134);">
					<th style="padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Status</th>
                        <th style="padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">NIK</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Nama</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Asal Departemen</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Asal Jabatan</th>      
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Rekomendasi</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Tujuan Departemen</th>
                        <th style="width: 5%; padding: 0;vertical-align: middle;font-size: 20px;background-color: #000000;color:white;">Tujuan Jabatan</th>
				</tr>
				<tr>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->status ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->mutasi_nik ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->mutasi_nama ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->mutasi_bagian ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->mutasi_jabatan1 ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->mutasi_rekomendasi ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->mutasi_ke_bagian ?></td>
					<td style="width: 6%; border:1px solid black;" align="center"><?= $row->mutasi_jabatan ?></td>
				</tr>
			</table>
			<p align="left">
				Status : 
			</p>
			<br>
			<table style="border-color: black">
				<tr style="background-color: rgb(126,86,134);">
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">3</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">2</th> 
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">1</th>
				</tr>
				<tr style="background-color: rgb(126,86,134);">
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Mengetahui, General Manager</th>
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Menyetujui, Manager Departeen</th> 
                        <th style="width: 15%; padding: 0;vertical-align: middle;font-size: 15px;background-color: #FFFF00;color:black;">Dibuat Oleh, Chief/Foreman</th>
				</tr>
				<tr>
			        <?php 
					if ( $row->gm <> 'Approved' && $row->gm <> 'Not Approved' && $row->gm <> 'Waiting') {
					 ?>
                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_gm ?></td>
                    <?php 
                    }
                    else{
                     ?>
                    <td style="width: 4%; border:1px solid black;" align="center"><?= $row->gm ?></td>
                    <?php 
                    }
                     ?>
                     <?php 
					if ( $row->manager <> 'Approved' && $row->manager <> 'Not Approved' && $row->manager <> 'Waiting') {
					 ?>
                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_manager ?></td>
                    <?php 
                    }
                    else{
                     ?>
                    <td style="width: 4%; border:1px solid black;" align="center"><?= $row->manager ?></td>
                    <?php 
                    }
                     ?>
                     <?php 
					if ( $row->chief_or_foreman <> 'Approved' && $row->chief_or_foreman <> 'Not Approved' && $row->chief_or_foreman <> 'Waiting') {
					 ?>
                    <td style="width: 4%; border:1px solid black; background-color:#FF8C00;" align="center"><?= $row->nama_chief ?></td>
                    <?php 
                    }
                    else{
                     ?>
                    <td style="width: 4%; border:1px solid black;" align="center"><?= $row->chief_or_foreman ?></td>
                    <?php 
                    }
                     ?>
				</tr>
				<!-- <tr>
						<td style="width: 6%; border:1px solid black;" align="center"><?= $row->date_direktur_hr ?></td>
						<td style="width: 6%; border:1px solid black;" align="center"><?= $row->date_pres_dir ?></td>
						<td style="width: 6%; border:1px solid black;" align="center"><?= $row->date_manager_hrga ?></td>
						<td style="width: 6%; border:1px solid black;" align="center"><?= $row->date_gm ?></td>
						<td style="width: 6%; border:1px solid black;" align="center"><?= $row->date_atasan_tujuan ?></td>
						<td style="width: 6%; border:1px solid black;" align="center"><?= $row->date_atasan_asal ?></td>
				</tr> -->
			</table>
			<br><br>
			<table align="center">
				<tr>
					<th style="background-color: #FFFF00;color:black;">Note</th>
				</tr>
				<tr>
					<td>Urutan Persetujuan : 1, 2, 3</td>
				</tr>
			</table>
			@endforeach			
		</center>
	</div>
</body>
</html>