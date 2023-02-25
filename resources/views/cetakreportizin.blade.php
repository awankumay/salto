@if(!empty($data))
@php
error_reporting(0);
header("Content-Type: application/force-download");
header("Cache-Control: no-chace, must-revalidate");
header("Content-disposition: attachment;filename=Laporan-Surat-".$data['judul'].".xls");
@endphp		
	@if($data['template']==1)
	<h3 align="center">
		<b> {{$data['judul']}} </b><br>
		<b> POLITEKNIK ILMU PEMASYARAKATAN</b><br><br>
    </h3>
	<table cellpadding="5" border="1" width="100%">
			<tr style="background-color:#b7b7b7">
			@foreach($data['header'] as $header)
				<td style="font-weight:bolder;">{{strtoupper($header)}}</td>
			@endforeach
			</tr>
            @foreach($data['body'] as $body)
            <tr>
				<tr>
					<td>{{$body['no']}}</td>
					<td>{{$body['name']}}</td>
					<td>{{$body['jenis_izin']}}</td>
					<td>{{$body['status_izin']}}</td>
					<td>{{$body['create_date']}}</td>
				</tr>
            </tr>
			@endforeach
           
	</table>
	@endif
</body>  
</html> 
@endif