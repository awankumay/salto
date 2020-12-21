@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('hukuman-dinas') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Hukuman Disiplin Taruna</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('hukuman-dinas-create'))
                        <a href="{{route('hukuman-dinas.create')}}" class="btn btn-danger btn-sm text-white btn-add">Tambah Hukuman</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap hukuman-dinas-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>STB</th>
                            <th>Taruna</th>
                            <th>Tingkat</th>
                            <th>Pembina</th>
                            <th>Hukuman</th>
                            <th>Tingkat Hukuman</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Akhir</th>
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
        var table = $('.hukuman-dinas-table').DataTable({
            processing: true,
            serverSide: true,
            order: [[ 0, "DESC" ], [7, "DESC"]],
            responsive: true,
            ajax: "{{ route('hukuman-dinas.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'stb', name: 'stb'},
                {data: 'nama_taruna', name: 'nama_taruna'},
                {data: 'grade_name', name: 'grade_name'},
                {data: 'nama_pembina', name: 'nama_pembina'},
                {data: 'hukuman_taruna', name: 'hukuman_taruna'},
                {data: 'tingkat', name: 'tingkat',
                    render:function(row, type, val, meta){
                        if(val.tingkat==1){
                            return '<span class="badge badge-info">Ringan</span>';
                        }else if(val.tingkat==2){
                            return '<span class="badge badge-warning">Sedang</span>';
                        }else{
                            return '<span class="badge badge-danger">Berat</span>';
                        }
                    }, orderable: false, searchable: false
                },
                {data: 'status', name: 'status',
                    render:function(row, type, val, meta){
                        if(val.status==1){
                            return '<span class="badge badge-success">Disetujui</span>';
                        }else if(val.status==2){
                            return '<span class="badge badge-danger">Dibatalkan</span>';
                        }else{
                            return '<span class="badge badge-warning">Belum Disetujui</span>';
                        }
                    }, orderable: false, searchable: false
                },
                {data: 'keterangan', name: 'keterangan'},
                {data: 'start_time', name: 'start_time'},
                {data: 'end_time', name: 'end_time'},
                {data: 'created_at', name: 'created_at'},
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
        let deleteUrl = 'hukuman-dinas/'+id;
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
                                let table = $('.hukuman-dinas-table').DataTable();
                                table.draw();
                                //window.location.reload();
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
