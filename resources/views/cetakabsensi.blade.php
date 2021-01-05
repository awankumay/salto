@if(!empty($data))
@php
error_reporting(0);
header("Content-Type: application/force-download");
header("Cache-Control: no-chace, must-revalidate");
header("Content-disposition: attachment;filename=Laporan-absensi-".$data['judul'].".xls");
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
                <td>{{$body['no']}}</td>
                <td>{{$body['nama']}}</td>
                <td>{{$body['stb']}}</td>
                <td>{{$body['in']}}</td>
                <td>{{$body['out']}}</td>
            </tr>
			@endforeach
           
	</table>
	@endif
</body>  
</html> 
@endif