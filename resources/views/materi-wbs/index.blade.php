@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('materi-wbs') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Materi WBS</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('materi-wbs-create'))
                        <a href="{{route('materi-wbs.create')}}" class="btn btn-danger btn-sm text-white btn-add">Tambah WBS</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table id ="materi-wbs-table" class="table display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Materi WBS</th>
                            {{-- <th>Opsi</th> --}}
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
<script>
    $(function() {
        $('#materi-wbs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('materi-wbs.index') !!}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nama_materi', name: 'nama_materi' },
            ]
        });
    });
    </script>
@endpush
@endsection
