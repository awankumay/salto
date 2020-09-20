@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('pengaduan') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Pengaduan</div>
                <div class="p-2">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table display nowrap report-table" style="widht:100%;">
                    <thead>
                        <tr>
                            <th style="width:5%;">ID</th>
                            <th style="width:25%;">Name</th>
                            <th style="width:25%;">Pengaduan</th>
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
        let table = $('.report-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('report.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'report', name:'report'},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'}
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
  </script>
@endpush
@endsection
