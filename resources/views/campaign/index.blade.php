@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('campaign') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Campaign</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('campaign-create'))
                        <a href="{{route('campaign.create')}}" class="btn btn-success btn-sm text-white btn-add">Tambah Campaign</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table display nowrap campaign-table" style="widht:100%;">
                    <thead>
                        <tr>
                            <th style="width:5%;">ID</th>
                            <th style="width:15%;">Judul</th>
                            <th style="width:5%;">Headline</th>
                            <th style="width:25%;">Status</th>
                            <th style="width:20%;">Ringkasan</th>
                            <th style="width:5%;">Content</th>
                            <th style="width:25%;">Author</th>
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
        let table = $('.campaign-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('campaign.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'headline', name: 'headline',
                    render:function(data){
                            if(data==1){
                                return '<span class="badge badge-success">Yes</span>';
                            }else{
                                return '<span class="badge badge-warning">No</span>';
                            }
                        }
                },
                {data: 'status', name: 'status',
                    render:function(data){
                            if(data==1){
                                return '<span class="badge badge-success">Active</span>';
                            }else{
                                return '<span class="badge badge-warning">Pending</span>';
                            }
                        }
                },
                {data: 'excerpt', name: 'excerpt'},
                {data: 'content', name: 'content'},
                {data: 'user_created', name: 'user_created'},
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
        let deleteUrl = 'campaign/'+id;
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
                                    toastr.success("Berhasil, Campaign berhasil dihapus");
			                    },500);
                                let i = row_index.parentNode.parentNode.rowIndex;
                                let table = $('.campaign-table').DataTable();
                                table.row(i).remove().draw();
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Gagal, Campaign tidak berhasil dihapus");
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
