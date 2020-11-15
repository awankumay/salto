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
		
	<p align="center">
		<b> SURAT IZIN SAKIT </b><br>
		<b> POLITEKNIK ILMU PEMASYARAKATAN</b><br><br>
	<p>
	
	<table cellpadding="5" border="1" width="100%" align="center">
			<tr style="background-color:#b7b7b7">
				<td>No</td>
				<td>NAMA</td>
				<td>NO STB</td>
				<td>KELUHAN</td>
				<td>DIAGNOSA</td>
				<td>REKOMENDASI</td>
				<td>DOKTER</td>
				<td>TANGGAL</td>
			</tr>
            <tr>
                    <td>No</td>
                    <td>NAMA</td>
                    <td>NO STB</td>
                    <td>KELUHAN</td>
                    <td>DIAGNOSA</td>
                    <td>REKOMENDASI</td>
                    <td>DOKTER</td>
                    <td>TANGGAL</td>
            </tr>
	</table>
	<p align="center" style="margin-left:40%">
		Depok, <!--  --><br>
		Ka. Sub Bagian Ketarunaan,<br>
		<!--$pdf->Image(base_url().'/assets/images/logo.png', 10, 10, 20);-->
		<img src="assets/img/jauhar.png"  alt="Logo" /> 
		<br><b><u>JAUHAR MUSTOFA</u></b><br>
		NIP. 197906092000121001<br>
	</p>
</body>  
</html> 
<!-- <meta http-equiv=refresh content=0;url=index.php?hl=izin_sakit> -->
@endif