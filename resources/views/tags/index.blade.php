@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('tags') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Tags</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('tags-create'))
                        <a href="{{route('tags.create')}}" class="btn btn-success btn-sm text-white btn-add">Tambah Tags</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table table-hover table-responsive table-bordered tags-table" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:5%">ID</th>
                            <th style="width:15%">Tags</th>
                            <th style="width:15%">Create</th>
                            <th style="width:15%">Author</th>
                            <th style="width:10%">Opsi</th>
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
        var table = $('.tags-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('tags.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
                {data: 'user_created', name: 'user_created'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });

    function deleteRecord(id, row_index) {
        let deleteUrl = 'tags/'+id;
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
                        let table = $('.tags-table').DataTable();
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
