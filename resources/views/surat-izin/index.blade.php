@extends('layouts.app')

@section('content')
<style>
.btn-no-focus:focus {
    outline: 0;
    box-shadow:0 0 0 0.2rem rgb(255 255 255);
}
</style>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('surat-izin') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Surat Izin</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('surat-izin-create'))
                        <a href="{{route('surat-izin.create')}}" class="btn btn-danger btn-sm text-white btn-add">Tambah Surat Izin</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="export-btn">
            <a class="btn btn-default btn-sm btn-no-focus" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                <i class="fas fa-file"> Export </i>
            </a>
            <div class="collapse" id="collapseExample">
            {!! Form::open(array('route' => ['exportdata'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
                    <div class="form-group col-md-4">
                        <strong>Start Date</strong>
                        <input type="date" required name="date_1" class="form-control form-control-sm"> 
                        <input type="hidden" name="data" value="surat-izin"> 
                    </div>
                    <div class="form-group col-md-4">
                        <strong>End Date</strong>
                        <input type="date" required name="date_2" class="form-control form-control-sm"> <br>
                        <button type="submit" class="btn btn-danger btn-sm">Export</button>
                    </div>
            {!! Form::close() !!}
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap surat-izin-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    $(function () {
        var table = $('.surat-izin-table').DataTable({
            processing: true,
            serverSide: true,
            order: [[ 3, "ASC" ], [0, "DESC"]],
  /*           rowReorder: {
                selector: 'td:nth-child(2)'
            }, */
            responsive: true,
            ajax: "{{ route('surat-izin.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'nama_menu', name: 'nama_menu', orderable: false, searchable: false},
                {data: 'status', name: 'status',
                    render:function(row, type, val, meta){
                        if(val.status==1){
                            return '<span class="badge badge-success">Disetujui</span>';
                        }else if(val.status==2){
                            return '<span class="badge badge-danger">Dibatalkan</span>';
                        }else{
                            return '<span class="badge badge-warning">Belum Disetujui</span>';
                        }
                    }, orderable: true, searchable: false
                },
                {data: 'created_at', name: 'created_at', orderable: true, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
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
        let deleteUrl = 'surat-izin/'+id;
        let token ="{{csrf_token()}}";
        swal({
                title: "Ingin menghapus data ini?",
                text: "Data ini tidak dapat dikembalikan jika telah terhapus",
                icon: "warning",
                buttons: true
            }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            beforeSend:function () {
                                $("#overlay").fadeIn(300);
                            },???
                            data: {
                            "_token": token,
                            },
                            success:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.success("Sukses, data berhasil dihapus");
			                    },500);
                                let i = row_index.parentNode.parentNode.rowIndex;
                                let table = $('.keluarga-asuh-table').DataTable();
                                table.draw();
                                window.location.reload();
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
  </script>
@endpush
@endsection
