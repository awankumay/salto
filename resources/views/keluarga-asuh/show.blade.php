@extends('layouts.app')

@section('content')
<style>
    .btn-tambah{line-height: 1;
    font-size: 11px !important;
    }
</style>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('keluarga-asuh.show', $keluargaAsuh) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
            {!! Form::open(array('route' => ['pembina-keluarga-asuh.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
                {{ csrf_field() }}
                <div class="p-2"> 
                    <div class="input-group mb-3">
                    {!! Form::number('keluarga_asuh_id', $keluargaAsuh->id, array('placeholder' => 'Whatsapp','class' => 'form-control form-control-sm', 'hidden')) !!}
                        @if(auth()->user()->hasPermissionTo('data-keluarga-asuh-edit'))
                        {!! Form::select('pembina_id', $pembina, [], array('class' => 'form-control pembina custom-select', 'single', 'placeholder'=>'Pilih Pembina', 'required')) !!} 
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-danger btn-sm btn-tambah" type="button">Tambah Pembina</button>
                        </div>
                        @endif
                    </div>
                    <span class="form-text {{isset($errors->messages()['pembina_id']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['pembina_id']) ? $errors->messages()['pembina_id'][0] .'' : ''}}
                    </span>
                </div>
                {!! Form::close() !!}
                <div class="p-2">
                    <a href="{{route('keluarga-asuh.index')}}" class="btn btn-warning btn-sm text-white btn-add">Kembali</a>
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap pembina-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pembina</th>
                            <th>Phone</th>
                            <th>Whatsapp</th>
                            <th>Create</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
            {!! Form::open(array('route' => ['waliasuh-keluarga-asuh.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
                {{ csrf_field() }}
                <div class="p-2"> 
                    <div class="input-group mb-3">
                    {!! Form::number('keluarga_asuh_id', $keluargaAsuh->id, array('placeholder' => 'Whatsapp','class' => 'form-control form-control-sm', 'hidden')) !!}
                    @if(auth()->user()->hasPermissionTo('data-keluarga-asuh-edit'))
                    {!! Form::select('waliasuh_id', $waliasuh, [], array('class' => 'form-control waliasuh custom-select', 'single', 'placeholder'=>'Pilih Wali Asuh', 'required')) !!}
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-danger btn-sm btn-tambah" type="button">Tambah Wali Asuh</button>
                        </div>
                        @endif
                    </div>
                    
                    <span class="form-text {{isset($errors->messages()['waliasuh_id']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['waliasuh_id']) ? $errors->messages()['waliasuh_id'][0] .'' : ''}}
                    </span>
                </div>
                {!! Form::close() !!}
                <div class="p-2">
                    <!-- <a href="{{route('keluarga-asuh.index')}}" class="btn btn-warning btn-sm text-white btn-add">Kembali</a> -->
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap waliasuh-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Wali Asuh</th>
                            <th>Phone</th>
                            <th>Whatsapp</th>
                            <th>Create</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
            {!! Form::open(array('route' => ['taruna-keluarga-asuh.store'],'method'=>'POST', 'enctype' => 'multipart/form-data')) !!}
                {{ csrf_field() }}
                <div class="p-2"> 
                    <div class="input-group mb-3">
                    {!! Form::number('keluarga_asuh_id', $keluargaAsuh->id, array('placeholder' => 'Whatsapp','class' => 'form-control form-control-sm', 'hidden')) !!}
                        @if(auth()->user()->hasPermissionTo('data-keluarga-asuh-edit'))
                        {!! Form::select('taruna_id', [], [], array('class' => 'form-control taruna custom-select', 'single', 'placeholder'=>'Pilih Taruna', 'required')) !!}
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-danger btn-sm btn-tambah" type="button">Tambah Taruna</button>
                        </div>
                        @endif
                    </div>
                    <span class="form-text {{isset($errors->messages()['taruna_id']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['taruna_id']) ? $errors->messages()['taruna_id'][0] .'' : ''}}
                    </span>
                </div>
                {!! Form::close() !!}
                <div class="p-2">
                    <!-- <a href="{{route('keluarga-asuh.index')}}" class="btn btn-warning btn-sm text-white btn-add">Kembali</a> -->
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap taruna-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Taruna</th>
                            <th>Phone</th>
                            <th>Whatsapp</th>
                            <th>Create</th>
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
        $('.pembina').select2();
        var table = $('.pembina-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('pembina-keluarga-asuh.index', ['id'=>$keluargaAsuh->id]) }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'whatsapp', name: 'whatsapp'},
                {data: 'date_created', name: 'date_created', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
        $("#DataTables_Table_0_filter input")
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

    $(function () {
        $('.waliasuh').select2();
        var table2 = $('.waliasuh-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('waliasuh-keluarga-asuh.index', ['id'=>$keluargaAsuh->id]) }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'whatsapp', name: 'whatsapp'},
                {data: 'date_created', name: 'date_created', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
        $("#DataTables_Table_1_filter input")
        .unbind()
        .bind("input", function(e) {
            if(this.value.length >= 3 || e.keyCode == 13) {
                table2.search(this.value).draw();
            }
            if(this.value == "") {
                table2.search("").draw();
            }
            return;
        });
    });

    $('.taruna').select2({
           minimumInputLength: 3,
           allowClear: true,
           placeholder: 'masukkan nama taruna',
           ajax: {
              dataType: 'json',
              url: '/dashboard/gettaruna',
              delay: 800,
              data: function(params) {
                return {
                  search: params.term
                }
              },
              processResults: function (data, page) {
              return {
                results: data
              };
            },
          }
      }).on('taruna:select', function (evt) {
         var data = $(".taruna option:selected").text();
      });

    $(function () {
        var table3 = $('.taruna-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('taruna-keluarga-asuh.index', ['id'=>$keluargaAsuh->id]) }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'whatsapp', name: 'whatsapp'},
                {data: 'date_created', name: 'date_created', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
        $("#DataTables_Table_2_filter input")
        .unbind()
        .bind("input", function(e) {
            if(this.value.length >= 3 || e.keyCode == 13) {
                table3.search(this.value).draw();
            }
            if(this.value == "") {
                table3.search("").draw();
            }
            return;
        });
    });


    function deleteRecord(id, row_index, model) {
        var getModel = model;
        if(getModel=='PembinaKeluargaAsuh'){
            var deleteUrl = '/dashboard/pembina-keluarga-asuh/'+id;
        }else if(getModel=='WaliasuhKeluargaAsuh'){
            var deleteUrl = '/dashboard/waliasuh-keluarga-asuh/'+id;
        }else{
            var deleteUrl = '/dashboard/taruna-keluarga-asuh/'+id;
        }
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
                                window.location.reload();
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
