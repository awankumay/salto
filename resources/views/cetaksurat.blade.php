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
	</style>
</head>  
<body>
    	<p align="center">
		KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA<br>
		REPUBLIK INDONESIA<br>
		BADAN PENGEMBANGAN SUMBER DAYA MANUSIA HUKUM DAN HAK ASASI MANUSIA<br>
		POLITEKNIK ILMU PEMASYARAKATAN<br>
		Jl. Raya Gandul No.4, Cinere, Depok 16512 Telp. (021) 7538421<br>
		website: <u>www.poltekip.ac.id</u><hr>
		
	@if($data['template']==1)
	<p align="center">
		<b> {{$data['category_name']}} </b><br>
		<b> POLITEKNIK ILMU PEMASYARAKATAN</b><br><br>
	<p>
	<table cellpadding="5" border="1" width="100%" align="center">
			<tr style="background-color:#b7b7b7">
			@foreach($data['header'] as $header)
				<td style="font-weight:bolder;text-transform:uppercase;">{{$header}}</td>
			@endforeach
			</tr>
            <tr>
			@foreach($data['body'] as $body)
				<td>{{$body}}</td>
			@endforeach
            </tr>
	</table>
	<p align="center" style="margin-left:40%">
		Depok, {{$data['tanggal_cetak']}}<!--  --><br>
		Ka. Sub Bagian Ketarunaan,<br>
        <!--$pdf->Image(base_url().'/assets/images/logo.png', 10, 10, 20);-->
        <br><br>
		<img src="jauhar.png"  alt="Logo" /> 
		<br><b><u>JAUHAR MUSTOFA</u></b><br>
		NIP. 197906092000121001<br>
	</p>
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