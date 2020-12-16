@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('pengaduan', '') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Detail Pengaduan</div>
                @if(auth()->user()->getRoleNames()[0] == "Super Admin")
                  @if(!empty($data) && empty($data->follow_up))
                    <div class="p-2">
                      <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">Beri Tanggapan</button>
                    </div>
                  @endif
                @endif
            </div>
        </div>
        <div class="card-body">
          @if(!empty($data))
              <table class="table table-row table-striped">
                <tbody>
                  <tr>
                    <td width="20%">Nama: </td>
                    <td width="80%">{{ $data->username }}</td>
                  </tr>
                  <tr>
                    <td>Pengaduan: </td>
                    <td>{{ $data->pengaduan }}</td>
                  </tr>
                  <tr>
                    <td>Tanggal Pengaduan: </td>
                    <td>{{ $data->created_at }}</td>
                  </tr>
                  <tr>
                    <td>Tanggapan: </td>
                    <td>{{ $data->follow_up }}</td>
                  </tr>
                </tbody>
              </table>
              <!-- Modal -->
              <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-body">
                    {!! Form::open(array('route' => ['report.update', $data->id],'method'=>'PATCH')) !!}
                      {!! Form::text('pengaduan', null, array('placeholder' => 'Pesan Tanggapan','class' => 'form-control','required' => 'required')) !!}
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                      </div>
                    {!! Form::close() !!}
                    </div>
                  </div>
                </div>
              </div>
            @else
              <h3>Maaf data anda tidak tersedia</h3>
            @endif
        </div>
    </div>
</div>

@endsection
