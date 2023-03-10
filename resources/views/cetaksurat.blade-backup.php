@if(!empty($data))

<html>  

<head>  
	<style>
		table{
			border-collapse: collapse;
		}
		table, th, td {
			border: 1px solid black;
            border-spacing: 0px;
            font-size:14px;
		}
		* {
		box-sizing: border-box;
		}

		/* Create two equal columns that floats next to each other */
		.column {
		/* margin-top: 15%; */
		float: left;
		width: 25%;
		/* padding: 10px;
		height: 300px; Should be removed. Only for demonstration */
		}

		.column2 {
		float: right;
		width: 35%;
		/* padding: 10px;
		height: 300px; Should be removed. Only for demonstration */
		}

		/* Clear floats after the columns */
		.row:after {
		content: "";
		display: table;
		clear: both;
		}
		.center {
		text-align: center;
		/* border: 3px solid green; */
		}
	</style>
	<title>Surat Izin</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>  
<body>
	<div class="container-fluid">
		<h1>Hello World!</h1>
		<p>Resize the browser window to see the effect.</p>
		<p>The columns will automatically stack on top of each other when the screen is less than 768px wide.</p>
		<div class="row">
		  <div class="col-sm-4" style="background-color:lavender;">.col-sm-4</div>
		  <div class="col-sm-8" style="background-color:lavenderblush;">.col-sm-8</div>
		</div>
	  </div>
    	{{-- <p align="center">
		KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA<br>
		REPUBLIK INDONESIA<br>
		BADAN PENGEMBANGAN SUMBER DAYA MANUSIA HUKUM DAN HAK ASASI MANUSIA<br>
		POLITEKNIK ILMU PEMASYARAKATAN<br>
		Jl. Raya Gandul No.4, Cinere, Depok 16512 Telp. (021) 7538421<br>
		website: <u>www.poltekip.ac.id</u><hr> --}}
		
	@if($data['template']==1)
	<p align="center">
		<b> {{$data['category_name']}} </b><br>
		<b> POLITEKNIK ILMU PEMASYARAKATAN</b><br><br>
	<p>
	<table cellpadding="5" border="1" width="100%" align="center">
			<tr style="background-color:#b7b7b7">
			@foreach($data['header'] as $header)
				<td style="font-weight:bolder;">{{strtoupper($header)}}</td>
			@endforeach
			</tr>
            <tr>
			@foreach($data['body'] as $body)
				<td>{{$body}}</td>
			@endforeach
            </tr>
	</table>
	<p>test</p>
	<table >
		<tr>
		  <td>Hari / Tanggal</td>
		  <td>:</td>
		  <td>Di Sini isi Tanggal</td>
		</tr>
		<tr>
		  <td>Waktu</td>
		  <td>:</td>
		  <td>Di Sini isi Tanggal</td>
		</tr>
		<tr>
		  <td>Tempat</td>
		  <td>:</td>
		  <td>Di Sini isi Tanggal</td>
		</tr>
	  </table>
{{-- TTD Start --}}
	<div class="row">
		{{-- <div class="column center">
			<br>
			Ka. Sub Bagian Ketarunaan,<br>
			<!--$pdf->Image(base_url().'/assets/images/logo.png', 10, 10, 20);-->
			<br><br>
			<img src="jauhar.png"  alt="Logo" /> 
			<br><b><u>JAUHAR MUSTOFA</u></b><br>
			NIP. 197906092000121001<br>
		</div> --}}
		<div class="column2 center">
			Depok, {{$data['tanggal_cetak']}}<!--  --><br>
			Ka. Sub Bagian Ketarunaan,<br>
			<!--$pdf->Image(base_url().'/assets/images/logo.png', 10, 10, 20);-->
			<br><br>
			<img src="jauhar.png"  alt="Logo" /> 
			<br><b><u>JAUHAR MUSTOFA</u></b><br>
			NIP. 197906092000121001<br>
		</div>
	  </div>
{{-- End TTD --}}
	@elseif($data['template']==2)
	<p align=center>
		<b><u> SURAT IZIN </u></b><br>
		<b> Nomor: SDM.5.SM.09.03 - {{$data['id_surat_cetak']}} </b><br>
		
	<p align=justify>
		&emsp;&emsp;&emsp; Sehubungan dengan Surat dari Permohonan Orangtua tanggal {{$data['created_at']}} tentang permohonan izin, dengan ini Direktur Politeknik Ilmu Pemasyarakatan Badan Pengembangan Sumber Daya Manusia Hukum dan Hak Asasi Manusia Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia memberikan izin kepada :<br>
	<br>
	<style>
		table{
			border-collapse: collapse;
		}
		table, th, td{
			border: 0px;
			border-spacing: 0px;
		}
		hr {
		  width: 100% solid black;
		border: 1px solid black;
		}
	</style>
	<table cellpadding="5" border="1" width="100%" align="center">
		<thead>
			
		</thead>
		<tbody>
			<tr align="left">
			<th>{{$data['header'][0]}} </th><td>{{$data['body'][0]}}</td>
			</tr>
			<tr align="left">
			<th>{{$data['header'][1]}}</th><td>{{$data['body'][1]}}</td>
			</tr>
			<tr align="left">
			<th>{{$data['header'][2]}}</th><td>{{$data['body'][2]}}</td>
			</tr>
			<tr align="left">
			<th>{{$data['header'][3]}}</th><td>{{$data['body'][3]}}</td>
			</tr>

			<tr align="left">
			<th>Ijin berlaku pada :</td>
			</tr>
			
			<tr align="left">
			<th>Tanggal Awal</th><td>{{$data['body'][4]}}</td>
			</tr>
			
			<tr align="left">
			<th>Tanggal Akhir</th><td>{{$data['body'][5]}}</td>
			</tr>
		
		</tbody>
	</table>
	
	<p align=justify>
		&emsp;&emsp;&emsp;Terlambat masuk kampus dinyatakan indisipliner dan akan dikenakan sanksi administratif kepegawaian dan sanksi akademik.
	<br>
	<p align=justify>
		&emsp;&emsp;&emsp;Demikian Izin ini diberikan untuk dapat dipergunakan sebagaimana mestinya dan kepada pihak yang terkait dimohon bantuan seperlunya.
	<br>
	
	<p align=left style="margin-left:60%">
		Depok, {{$data['tanggal_cetak']}}<br>
		Direktur,<br>
		<img src="direktur_spesimen.png"  alt="Logo" /> 
		<br><b><u>Dr. Rachmayanthy, Bc.IP., SH., M.Si</u></b><br>
		NIP. 196904261992032001<br>
	</p>
	
	<p align=left style="margin-left:5%">
		<img src="qr_dir.png"  alt="Logo" /> 
	</p>
	@elseif($data['template']==3)
	<p align=center>
		<b><u> SURAT KETERANGAN </u></b><br>
		<b> Nomor: SDM.5.SM.09.03 - {{$data['id_surat_cetak']}} </b><br>
		
	<p align=justify>
		&emsp;&emsp;&emsp; Direktur Politeknik Ilmu Pemasyarakatan Badan Pengembangan Sumber Daya Manusia Hukum dan Hak Asasi Manusia Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia dengan ini menerangkan bahwa :<br>
	<br>
	<style>
		table{
			border-collapse: collapse;
		}
		table, th, td{
			border: 0px;
			border-spacing: 0px;
		}
		hr {
		  width: 100% solid black;
		border: 1px solid black;
		}
	</style>
	<table cellpadding="5" border="1" width="100%" align="center">
		<thead>
			
		</thead>
		<tbody>
			<tr align="left">
			<th style="width:30%;">{{$data['header'][0]}} </th><td>{{ucwords($data['body'][0])}}</td>
			</tr>
			<tr align="left">
			<th style="width:30%;">{{$data['header'][1]}}</th><td>{{ucwords($data['body'][1])}}</td>
			</tr>
			<tr align="left">
			<th style="width:30%;">{{$data['header'][2]}}</th><td>{{ucwords($data['body'][2])}}</td>
			</tr>
			<tr align="left">
			<th style="width:30%;">{{$data['header'][3]}}</th><td>{{ucwords($data['body'][3])}}</td>
			</tr>
			<tr align="left">
			<th style="width:30%;">{{$data['header'][4]}}</th><td>{{ucwords($data['body'][4])}}</td>
			</tr>
			<tr align="left">
			<th style="width:30%;">{{$data['header'][5]}}</th><td>{{ucwords($data['body'][5])}}</td>
			</tr>
			<tr align="left">
			<th style="width:30%;">{{$data['header'][6]}}</th><td>{{ucwords($data['body'][6])}}</td>
			</tr>
		
		</tbody>
	</table>
	
	<p align=justify>
		&emsp;&emsp;&emsp;Sampai dengan Surat Keterangan ini dibuat yang bersangkutan Taruna Politeknik Ilmu Pemasyarakatan yang masih menjalani Pendidikan di Politeknik Ilmu Pemasyarakatan Badan Pengembangan Sumber Daya Manusia Hukum dan Hak Asasi Manusia Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia.
	<br>
	<p align=justify>
		&emsp;&emsp;&emsp;Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan dengan sebaik-baiknya.
	<br>
	
	<p align=left style="margin-left:60%">
		Depok, {{$data['tanggal_cetak']}}<br>
		Direktur,<br>
		<img src="direktur_spesimen.png"  alt="Logo" /> 
		<br><b><u>Dr. Rachmayanthy, Bc.IP., SH., M.Si</u></b><br>
		NIP. 196904261992032001<br>
	</p>
	
	<p align=left style="margin-left:5%">
		<img src="qr_dir.png"  alt="Logo" /> 
	</p>
	@endif
</body>  
</html> 
@endif