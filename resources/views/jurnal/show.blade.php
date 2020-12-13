@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('jurnal.show', $jurnal) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Detail Jurnal</div>
                <div class="p-2">
                @if(auth()->user()->hasPermissionTo('jurnal-harian-create') && $jurnal->tanggal==date('d-m-Y'))
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#tambahJurnal">
                       Tambah Jurnal
                    </button>
                @endif
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap jurnal-detail-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Kegiatan</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editJurnal" tabindex="-1" role="dialog" aria-labelledby="editJurnalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJurnalLabel">Edit Jurnal</h5>
                    <button type="button" class="close" onclick="refresh();" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="edit_jurnal" id="edit_jurnal" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_user" value="{{$jurnal->id_user}}">
                        <div class="form-group col-md-10">
                            <strong>Mulai</strong>
                            <input type="time" class="form-control form-control-sm" name="start_time" value="{{date_format(date_create($jurnal->start_time), 'H:i')}}">
                            <span class="form-text">
                                <span class="text-danger text-help" id="start_time-error"></span>
                            </span>
                        </div>
                        <div class="form-group col-md-10">
                            <strong>Akhir</strong>
                            <input type="time" class="form-control form-control-sm" name="end_time" value="{{date_format(date_create($jurnal->end_time), 'H:i')}}">
                            <span class="form-text">
                                <span class="text-danger text-help" id="end_time-error"></span>
                            </span>
                        </div>
                        <div class="form-group col-md-10">
                            <strong>Kegiatan</strong>
                            <input type="text" class="form-control form-control-sm" name="kegiatan" value="{{$jurnal->kegiatan}}">
                            <span class="form-text">
                                <span class="text-danger text-help" id="kegiatan-error"></span>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="refresh();">Close</button>
                        <button type="button" id="edit_jurnal_btn" class="btn btn-danger">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahJurnal" tabindex="-1" role="dialog" aria-labelledby="tambahJurnalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahJurnalLabel">Tambah Jurnal</h5>
                    <button type="button" class="close" onclick="refresh();" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="tambah_jurnal" id="tambah_jurnal" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_user">
                        <div class="form-group col-md-10">
                            <strong>Mulai</strong>
                            <input type="time" class="form-control form-control-sm" name="start_time">
                            <span class="form-text">
                                <span class="text-danger text-help" id="start_time-error"></span>
                            </span>
                        </div>
                        <div class="form-group col-md-10">
                            <strong>Akhir</strong>
                            <input type="time" class="form-control form-control-sm" name="end_time">
                            <span class="form-text">
                                <span class="text-danger text-help" id="end_time-error"></span>
                            </span>
                        </div>
                        <div class="form-group col-md-10">
                            <strong>Kegiatan</strong>
                            <input type="text" class="form-control form-control-sm" name="kegiatan">
                            <span class="form-text">
                                <span class="text-danger text-help" id="kegiatan-error"></span>
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="refresh();">Close</button>
                        <button type="button" id="tambah_jurnal_btn" class="btn btn-danger">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    $(function () {
        const url = "{{ route('jurnaldetail', ['id_user'=>$jurnal->id_user, 'date'=>$jurnal->tanggal]) }}";
        const parseResult = new DOMParser().parseFromString(url, "text/html");
        const parsedUrl = parseResult.documentElement.textContent;
        var table = $('.jurnal-detail-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: parsedUrl,
            columns: [
                {data: 'id', name: 'id'},
                {data: 'nama', name: 'nama'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'kegiatan', name: 'kegiatan'},
                {data: 'start_time', name: 'start_time'},
                {data: 'end_time', name: 'end_time'},
                {data: 'action', name: 'action', orderable: false, searchable: false, 
                    render:function(row, type, val, meta){
                        if (val.tanggal!=new Date().toISOString().slice(0, 10)) {
                            return '';
                        }else{
                            return val.action;
                        }
                    }
                },
            ]
        });
        $(".dataTables_filter input")
        .unbind()
        .bind("input", function(e) {
            if(this.value.length >= 3 || e.keyCode == 13) {
                table.search(this.value).draw();
            }
            if(this.value == "") {
                table.search("").draw();
            }
            return;
        });
    });

    function deleteRecord(id, row_index) {
        let deleteUrl = "{{url('/')}}/dashboard/jurnal/"+id;
        let token ="{{csrf_token()}}";
        swal({
                title: "Ingin menghapus data ini?",
                text: "Data ini tidak dapat dikembalikan jika telah terhapus",
                icon: "warning",
                buttons: true
            }).
        then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    beforeSend:function () {
                        $("#overlay").fadeIn(300);
                    },　
                    data: {
                    "_token": token,
                    },
                    success:function(){
                        setTimeout(function(){
                            $("#overlay").fadeOut(300);
                            toastr.success("Sukses, data berhasil dihapus");
                        },500);
                        let i = row_index.parentNode.parentNode.rowIndex;
                        let table = $('.jurnal-detail-table').DataTable();
                        table.draw();
                    },
                    error:function(){
                        setTimeout(function(){
                            $("#overlay").fadeOut(300);
                            toastr.error("Gagal, data gagal dihapus");
                        },500);
                    }
                });
            } else {
                //swal("Your imaginary file is safe!");
            }
        });
    }

    function refresh() {
        window.location.reload()
    }

    $(function() {
        $('#edit_jurnal_btn').on('click', function(e){
            var fd = new FormData(document.querySelector('#edit_jurnal'));  
            fd.append('id', document.querySelector('#btnJurnal').dataset.id);					      
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
            $.ajax({
                url: "{{url('/')}}/dashboard/inputjurnal", 
                data: fd,
                processData: false,
                contentType: false,
                beforeSend:function () {
                                $("#overlay").fadeIn(300);
                },
                type: 'POST',
                success: function(data, textStatus, xhr){
                    $("#overlay").fadeOut();
                    toastr.success("Sukses", data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                },
                error : function(data, textStatus, xhr) {
                    $("#overlay").fadeOut();
                    if(data.responseJSON.error){
                        if(data.responseJSON.error.kegiatan){
                            $('#kegiatan-error').text(data.responseJSON.error.kegiatan[0])
                        }
                        if(data.responseJSON.error.start_time){
                            $('#start_time-error').text(data.responseJSON.error.start_time[0])
                        }
                        if(data.responseJSON.error.end_time){
                            $('#end_time-error').text(data.responseJSON.error.end_time[0])
                        }
                    }
                }
            });
        }); 

        $('#tambah_jurnal_btn').on('click', function(e){
            var fd = new FormData(document.querySelector('#tambah_jurnal'));  
            fd.append('id_user', "{{$jurnal->id_user}}");			
            fd.append('tanggal', "{{$jurnal->tanggal}}");			
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
            $.ajax({
                url: "{{url('/')}}/dashboard/inputjurnal", 
                data: fd,
                processData: false,
                contentType: false,
                beforeSend:function () {
                                $("#overlay").fadeIn(300);
                },
                type: 'POST',
                success: function(data, textStatus, xhr){
                    $("#overlay").fadeOut();
                    toastr.success("Sukses", data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                },
                error : function(data, textStatus, xhr) {
                    $("#overlay").fadeOut();
                    if(data.responseJSON.error){
                        if(data.responseJSON.error.kegiatan){
                            $('#kegiatan-error').text(data.responseJSON.error.kegiatan[0])
                        }
                        if(data.responseJSON.error.start_time){
                            $('#start_time-error').text(data.responseJSON.error.start_time[0])
                        }
                        if(data.responseJSON.error.end_time){
                            $('#end_time-error').text(data.responseJSON.error.end_time[0])
                        }
                    }
                }
            });
        }); 
    });

    
  </script>
@endpush
@endsection
