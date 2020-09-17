@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('transaction') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Daftar Belanja</div>
                <div class="p-2">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table display nowrap transaction-table" style="widht:100%;">
                    <thead>
                        <tr>
                            <th style="width:5%;">ID Transaksi</th>
                            <th style="width:15%;">Pengguna</th>
                            <th style="width:15%;">ID Kunjungan</th>
                            <th style="width:25%;">Pengunjung</th>
                            <th style="width:25%;">Tgl Transaksi</th>
                            <th style="width:25%;">Tgl Bayar</th>
                            <th style="width:25%;">Status</th>
                            <th style="width:25%;">Total Qty</th>
                            <th style="width:25%;">Total Harga</th>
                            <th style="width:25%;">Action</th>
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
        let table = $('.transaction-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('transaction.index') }}",
            columnDefs: [ { type: 'date', 'targets': [4] } ],
            order: [[ 4, 'desc' ]],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'userapp', name: 'userapp'},
                {data: 'id_visit', name: 'id_visit'},
                {data: 'visitor_name', name: 'visitor_name'},
                {data: 'created_at', name: 'created_at'},
                {data: 'date_payment', name: 'date_payment'},
                {data: 'trans_status', name: 'trans_status'},
                {data: 'tqty', name: 'tqty'},
                {data: 'tprice', name: 'tprice'},
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
        let deleteUrl = 'visitor/'+id;
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
                                    toastr.success("Berhasil, produk berhasil dihapus");
			                    },500);
                                let i = row_index.parentNode.parentNode.rowIndex;
                                let table = $('.visitor-table').DataTable();
                                table.row(i).remove().draw();
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Gagal, produk tidak berhasil dihapus");
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
