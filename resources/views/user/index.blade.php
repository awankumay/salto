@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('user') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2"> Pengguna</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('user-create'))
                        <a href="{{route('user.create')}}" class="btn btn-danger btn-sm text-white btn-add">Tambah Pengguna</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table display nowrap user-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>STB</th>
                            <th>Nama</th>
                            <th>Foto</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Whatsapp</th>
                            <th>Jk</th>
                            <th>Status</th>
                            <th>Alamat</th>
                            <th>Create</th>
                            <th>Update</th>
                            <th>Delete</th>
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
        let table = $('.user-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('user.index') }}",
            columns: [
                {data: 'stb', name: 'stb'},
                {data: 'name', name: 'name'},
                {data: 'photo', name: 'photo',
                    render:function(row, type, val, meta){
                        if(val.photo){
                            return "<img src=\"" + "/storage/{{config('app.userImagePath')}}/"+val.photo+ "\" height=\"50\"/>";
                        }else{
                            return "<img src=\"" + "/profile.png"+"\" height=\"50\"/>";
                        }
                    }, orderable: false, searchable: false
                },
                {data: 'email', name: 'email', orderable: false, searchable: false},
                {data: 'phone', name: 'phone', orderable: false, searchable: false},
                {data: 'whatsapp', name: 'whatsapp', orderable: false, searchable: false},
                {data: 'sex', name: 'sex',
                    render:function(data){
                        if(data==1){
                            return '<i class="fa fa-male fa-2x" aria-hidden="true"></i>';
                        }else{
                            return '<i class="fa fa-female fa-2x" aria-hidden="true"></i>';
                        }
                    }, orderable: false, searchable: false
                },
                {data: 'status', name: 'status',
                    render:function(row, type, val, meta){
                        if(val.status==1){
                            return '<span class="badge badge-success">Active</span>';
                        }else{
                            return '<span class="badge badge-warning">Not Active</span>';
                        }
                    }, orderable: false, searchable: false
                },
                {data: 'address', name: 'address', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at', orderable: false, searchable: false},
                {data: 'updated_at', name: 'updated_at', orderable: false, searchable: false},
                {data: 'deleted_at', name: 'deleted_at', orderable: false, searchable: false},
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
        let deleteUrl = 'user/'+id;
        let token ="{{csrf_token()}}";
        swal({
                title: "Ingin menghapus data ini?",
                text: "Data ini tidak dapat dikembalikan jika telah terhapus",
                icon: "warning",
                buttons: true
            }).then((willDelete) => {
                    if (willDelete) {
                        $(document).ajaxSend(function() {
                            $("#overlay").fadeIn(300);　
                        });
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            data: {
                            "_token": token,
                            },
                            success:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.success("Sukses, Data berhasil dihapus");
			                    },500);
                                let i = row_index.parentNode.parentNode.rowIndex;
                                let table = $('.user-table').DataTable();
                                table.row(i).remove().draw();
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Error, Data gagal dihapus");
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
