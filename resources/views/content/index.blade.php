@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('content') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Konten</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('berita-create'))
                        <a href="{{route('content.create')}}" class="btn btn-danger btn-sm text-white btn-add">Tambah Berita & Informasi</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table display nowrap post-table" style="widht:100%;">
                    <thead>
                        <tr>
                            <th style="width:5%;">ID</th>
                            <th style="width:15%;">Judul</th>
                            <th style="width:25%;">Status</th>
                            <th style="width:20%;">Ringkasan</th>
                            <th style="width:5%;">Content</th>
                            <th style="width:25%;">Create</th>
                            <th style="width:25%;">Update</th>
                            <th style="width:10%">Action</th>
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
        let table = $('.post-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            columnDefs: [ { type: 'date', 'targets': [3] } ],
            order: [[ 3, 'desc' ]],
            ajax: "{{ route('content.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'status', name: 'status',
                    render:function(data){
                            if(data==1){
                                return '<span class="badge badge-success">Active</span>';
                            }else{
                                return '<span class="badge badge-warning">Pending</span>';
                            }
                        }
                },
                {data: 'headline', name: 'headline',
                    render:function(data){
                            if(data==1){
                                return '<span class="badge badge-success">Ya</span>';
                            }else{
                                return '<span class="badge badge-warning">Tidak</span>';
                            }
                        }
                },
                {data: 'photo', name: 'photo',
                    render:function(row, type, val, meta){
                        if(val.photo){
                            return "<img src=\"" + "/storage/{{config('app.postImagePath')}}/"+val.photo+ "\" height=\"50\"/>";
                        }else{
                            return "-";
                        }
                    }, orderable: false, searchable: false
                },
                {data: 'excerpt', name: 'excerpt'},
                {data: 'content', name: 'content'},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
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
        let deleteUrl = 'content/'+id;
        let token ="{{csrf_token()}}";
        swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this data!",
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
                                    toastr.success("Berhasil, Konten berhasil dihapus");
			                    },500);
                                let i = row_index.parentNode.parentNode.rowIndex;
                                let table = $('.post-table').DataTable();
                                table.row(i).remove().draw();
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Gagal, Konten tidak berhasil dihapus");
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
