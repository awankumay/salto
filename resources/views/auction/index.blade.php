@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('auction') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Auction</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('auction-create'))
                        <a href="{{route('auction.create')}}" class="btn btn-success btn-sm text-white btn-add">Tambah Auction</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table display nowrap auction-table" style="widht:100%;">
                    <thead>
                        <tr>
                            <th style="width:5%;">ID</th>
                            <th style="width:15%;">Judul</th>
                            <th style="width:5%;">Headline</th>
                            <th style="width:25%;">Status</th>
                            <th style="width:20%;">Start Price</th>
                            <th style="width:5%;">Buy Now?</th>
                            <th style="width:25%;">Price Buy Now</th>
                            <th style="width:25%;">Author</th>
                            <th style="width:25%;">Date Published</th>
                            <th style="width:25%;">Date Created</th>
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
        let table = $('.auction-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('auction.index') }}",
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
                {data: 'start_price', name: 'start_price'},
                {data: 'buy_now', name: 'buy_now'},
                {data: 'price_buy_now', name: 'price_buy_now'},
                {data: 'user_created', name: 'user_created'},
                {data: 'date_published', name: 'date_published'},
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
        let deleteUrl = 'auction/'+id;
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
                                    toastr.success("Berhasil, auction berhasil dihapus");
			                    },500);
                                let i = row_index.parentNode.parentNode.rowIndex;
                                let table = $('.auction-table').DataTable();
                                table.row(i).remove().draw();
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Gagal, auction tidak berhasil dihapus");
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
