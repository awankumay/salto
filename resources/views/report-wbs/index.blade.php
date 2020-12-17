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
                    @if(auth()->user()->hasPermissionTo('pengaduan-wbs-create'))
                        <a href="{{route('reportwbs.create')}}" class="btn btn-danger btn-sm text-white btn-add">Tambah Pengaduan</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table display nowrap report-table" style="widht:100%;">
                    <thead>
                        <tr>
                            <th style="width:5%;">ID</th>
                            <th style="width:15%%;">Username</th>
                            <th style="width:15%%;">Materi</th>
                            <th style="width:15%%;">What</th>
                            <th style="width:15%%;">Who</th>
                            <th style="width:15%%;">Why</th>
                            <th style="width:15%%;">When</th>
                            <th style="width:15%%;">Where</th>
                            <th style="width:15%%;">Tanggal Pengaduan</th>
                            <th style="width:15%%;">Action</th>
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
            ajax: "{{ route('reportwbs.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'username', name: 'username'},
                {data: 'materi', name: 'materi'},
                {data: 'ewhat', name: 'ewhat'},
                {data: 'ewho', name: 'ewho'},
                {data: 'ewhy', name: 'ewhy'},
                {data: 'ewhen', name: 'ewhen'},
                {data: 'ewhere', name: 'ewhere'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action'}
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
