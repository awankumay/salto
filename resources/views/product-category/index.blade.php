@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('product-category') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Produk Kategori</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('product-category-create'))
                        <a href="{{route('product-category.create')}}" class="btn btn-success btn-sm text-white btn-add">Tambah Kategori</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap product-category-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Create</th>
                            <th>Update</th>
                            <th>Author</th>
                            <th>Opsi</th>
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
        var table = $('.product-category-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('product-category.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
                {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'user_created', name: 'user_created'},
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
        let deleteUrl = 'product-category/'+id;
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
                                let table = $('.product-category-table').DataTable();
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
  </script>
@endpush
@endsection
