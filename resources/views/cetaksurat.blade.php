@if(!empty($data))
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>[INTERNAL] POLITEKNIK ILMU PEMASYARAKATAN - SURAT IZIN</title>
	{{-- start style --}}
	<style type= "text/css">
		table{
			border-collapse: collapse;
		}
		table, th, td {
			border: 0px solid black;
            border-spacing: 1px;
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
		/* width: 35%; */
		/* padding: 10px;
		height: 300px; Should be removed. Only for demonstration */
		}
		.posisionTTD{
		position: relative;
		float: right;
		/* top: 400px; */
		}
		.tab {
            display: inline-block;
            margin-left: 40px;
        }
		.categorySurat{
			position: relative;
			left: 35%;
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
	</style >
	{{-- end style --}}
</head>
<body>
	{{-- Start Header Surat --}}
	<div>
		<table class="fixed">
			<tr>
				<td width="50px" height="50px">
					<img src="LOGO-KMHM.png" width="80px" alt="Logo" /> 
				</td>
				<td>
					<p align="center">
						KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA<br>
						REPUBLIK INDONESIA<br>
						BADAN PENGEMBANGAN SUMBER DAYA MANUSIA HUKUM DAN HAK ASASI MANUSIA<br>
						POLITEKNIK ILMU PEMASYARAKATAN<br>
						Jl. Raya Gandul No.4, Cinere, Depok 16512 Telp. (021) 7538421<br>
						website: <u>www.poltekip.ac.id</u>
					</p>
				</td>
			</tr>
		</table>
		<hr style="height:5px;border-width:0;color:rgb(0, 0, 0);background-color:rgb(0, 0, 0)">
	</div>
	{{-- End Header Surat --}}
	{{-- Start Body Surat --}}
	{{-- Start Template 1 --}}
	@if($data['template']==1)
	<div>
		<div style="float: right">
			Depok, {{$data['tanggal_cetak']}}
		</div>
		<div>
			<div>
				<br>
				<p class="categorySurat">
					<u>{{$data['category_name']}}</u>
					<br>
					{{-- Nomor Surat : xxxxxxxx --}}
				</p>
			</div>
			<p>
				<span class="tab"></span>Dengan Hormat, <br>
				Dengan detail yang bersangkutan di bawah ini :
			</p>
			<table>
				<tr>
					<td>
						Nama
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['name']}}
					</td>
				</tr>
				<tr>
					<td>
						STB
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['no_stb']}}
					</td>
				</tr>
				<tr>
					<td>
						Tingkat
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['grade']}}
					</td>
				</tr>
				{{-- Keperluan or Traning --}}
				{{-- <tr>
					<td>
						{{$data['header_keperluan'] != null ? $data['header_keperluan']:$data['header_keluhan']}}
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['keluhan'] != null ? $data['keluhan']:$data['keperluan']}}
					</td>
				</tr> --}}
				@if($data['header_keperluan'] == 'Keperluan')
				<tr>
					<td>
						{{$data['header_keperluan']}}
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['keperluan']}}
					</td>
				</tr>
				@elseif($data['header_keluhan'] == 'Keluhan')
				<tr>
					<td>
						{{$data['header_keluhan']}}
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['keluhan']}}
					</td>
				</tr>
				@else
				<tr>
					<td>
						Training
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['training']}}
					</td>
				</tr>
				<tr>
					<td>
						Pelatih
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['pelatih']}}
					</td>
				</tr>
				@endif
				{{-- End Keperluan or Traning --}}
				@if($data['header_tujuan'] == null)
				<tr>
					<td></td>
				</tr>
				@else
				<tr>
					<td>
						{{$data['header_tujuan'] != null ? $data['header_tujuan']:$data['header_diagnosa']}}
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['diagnosa'] != null ? $data['diagnosa']:$data['tujuan']}}
					</td>
				</tr>
				@endif
				{{-- End Tujuan or Diagnosa --}}
				@if($data['header_dokter'] != null)
				<tr>
					<td>
						Dokter
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['dokter']}}
					</td>
				</tr>
				<tr>
					<td>
						Rekomendasi
					</td>
					<td>
						:
					</td>
					<td>
						{{$data['rekomendasi']}}
					</td>
				</tr>
				@else
				<tr>
					<td></td>
				</tr>
				@endif
			</table>
			{{-- <table>
				<tr>
				@foreach($data['header'] as $header)
					<td>{{strtoupper($header)}}</td>
				@endforeach
				</tr>
				<tr>
					@foreach($data['body'] as $body)
						<td>{{$body}}</td>
					@endforeach
				</tr>
			</table> --}}
			<p>
				<span class="tab"></span>Disampaikan untuk melaksanakan kegiatan tersebut dengan tanggung jawab dan menjadi norma-norma yang ada.
			</p>
		</div>
	</div>
	<div class="row">
		<div class="posisionTTD center">
			Ka. Sub Bagian Ketarunaan,<br>
			<!--$pdf->Image(base_url().'/assets/images/logo.png', 10, 10, 20);-->
			<br><br>
			{{-- <img src="jauhar.png"  alt="Logo" />  --}}
			<b>TTD</b>
			<br><br>
			<br><b><u>PUTRANTO PRI HARDOKO, A.Md.I.P, S.H</u></b><br>
			NIP. 198304272001121001<br>
		</div>
	  </div>
	{{-- End Template 1 --}}
	{{-- Start Template 2 --}}
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

			{{-- <tr align="left">
			<th>Ijin berlaku pada :</td>
			</tr> --}}
			
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
	
	<p align=center style="margin-left:60%">
		Depok, {{$data['tanggal_cetak']}}<br>
		Direktur,<br>
		<img src="direktur_spesimen.png"  alt="Logo" /> 
		<br><b><u>Dr. Rachmayanthy, Bc.IP., SH., M.Si</u></b><br>
		NIP. 196904261992032001<br>
	</p>
	
	<p align=left style="margin-left:5%">
		<img src="qr_dir.png"  alt="Logo" /> 
	</p>
	{{-- End Template 2 --}}
	{{-- Start Template 3 --}}
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
	{{-- End Template 3 --}}
	{{-- End Body Surat --}}
	
	@endif
</body>
</html>
@endif