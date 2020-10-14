@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('visitor') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Daftar Kunjungan</div>
                <div class="p-2">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table display nowrap visitor-table" style="widht:100%;">
                    <thead>
                        <tr>
                            <th style="width:5%;">ID</th>
                            <th style="width:15%;">User</th>
                            <th style="width:15%;">Tlp</th>
                            <th style="width:25%;">WA</th>
                            <th style="width:25%;">Tgl Kunjungan</th>
                            <th style="width:25%;">Jadwal</th>
                            <th style="width:25%;">Pengunjung</th>
                            <th style="width:25%;">Antrian</th>
                            <th style="width:25%;">Napi</th>
                            <th style="width:25%;">Status</th>
                            <th style="width:25%;">Belanja</th>
                            <th style="width:25%;">Dibuat</th>
                            <th style="width:25%;">Diubah</th>

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
        let table = $('.visitor-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('visitor.index') }}",
            columnDefs: [ { type: 'date', 'targets': [4], render:function(data){
                return moment(data).format('D-M-YYYY');
            } } ],
            order: [[ 4, 'desc' ]],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'username', name: 'username'},
                {data: 'phone', name: 'phone'},
                {data: 'whatsapp', name: 'whatsapp'},
                {data: 'tgl_hadir', 
                    name: 'tgl_hadir', 
                    type: 'date',
                    displayFormat: 'D/M/YYYY'},
                {data: 'schedule', name: 'schedule'},
                {data: 'visitor', name: 'visitor'},
                {data: 'no_antrian', name: 'no_antrian'},
                {data: 'napi', name: 'napi'},
                {data: 'tipe_napi', name: 'tipe_napi'},
                {data: 'tipe_kunjungan', name: 'tipe_napi'},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},


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
