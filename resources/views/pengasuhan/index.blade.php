@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('pengasuhan') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Pengasuhan Daring</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('pengasuhan-daring-create'))
                         <a href="{{route('pengasuhan.create')}}" class="btn btn-danger btn-sm text-white btn-add">Tambah</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap pengasuhan-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Keluarga Asuh</th>
                            <th>Media</th>
                            <th>Mulai</th>
                            <th>Akhir</th>
                            <th>ID Media</th>
                            <th>Password</th>
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
        var table = $('.pengasuhan-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('pengasuhan.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'keluarga_asuh', name: 'keluarga_asuh'},
                {data: 'media', name: 'media',  orderable: false, searchable: false},
                {data: 'start_time', name: 'start_time',  orderable: false, searchable: false},
                {data: 'end_time', name: 'end_time',  orderable: false, searchable: false},
                {data: 'id_media', name: 'id_media'},
                {data: 'password', name: 'password'},
                {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
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
        let deleteUrl = 'pengasuhan/'+id;
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
                                let table = $('.pengasuhan-table').DataTable();
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
