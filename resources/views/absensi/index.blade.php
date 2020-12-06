@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('absensi') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Absensi</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('absensi-create') && $clockIn==null)
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#clockIn">
                        Clock In
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="clockIn" tabindex="-1" role="dialog" aria-labelledby="clockInLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="clockInLabel">Clock In</h5>
                                    <button type="button" class="close" onclick="refresh();" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form name="clock_in" id="clock_in" method="post" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <input type="file" name="file_clock_in" id="file_clock_in">
                                        <span class="form-text">
                                            <span class="text-danger text-help" id="file-error"></span>
                                        </span>
                                        <input type="hidden" name="id_user" id="id_user" value="{{Auth::user()->id}}">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="refresh();">Close</button>
                                        <button type="button" id="clock_in_btn" class="btn btn-danger">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(auth()->user()->hasPermissionTo('absensi-create') && $clockOut==null)
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#clockOut">
                        Clock Out
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="clockOut" tabindex="-1" role="dialog" aria-labelledby="clockOutLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="clockOutLabel">Clock Out</h5>
                                    <button type="button" class="close" onclick="refresh();" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form name="clock_out" id="clock_out">
                                    <div class="modal-body">
                                        <input type="file" name="file_clock_out" id="file_clock_out">
                                        <input type="hidden" name="id_user" id="id_user" value="{{Auth::user()->id}}">
                                        <span class="form-text">
                                            <span class="text-danger text-help" id="file-error"></span>
                                        </span>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" onclick="refresh();">Close</button>
                                        <button type="button" id="clock_out_btn" class="btn btn-danger">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="cards card-body">
            <div class="table table-responsive">
                <table class="table display nowrap absensi-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>STB</th>
                            <th>Nama</th>
                            <th>Clock In</th>
                            <th>Photo In</th>
                            <th>Clock Out</th>
                            <th>Photo Out</th>
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
    $(function() {
        $('#clock_in_btn').on('click', function(e){
            var fd = new FormData(document.querySelector('#clock_in'));    
            // fd.append( 'file', $('#anything_else_you_want_to_add') );					      
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
            let urlUpload = 'clockin';
            $.ajax({
                url: "{{url('/')}}/dashboard/"+urlUpload, 
                data: fd,
                processData: false,
                contentType: false,
                beforeSend:function () {
                                $("#overlay").fadeIn(300);
                },
                type: 'POST',
                success: function(data, textStatus, xhr){
                    $("#overlay").fadeOut();
                    toastr.success("Sukses, Clock In Berhasil");
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                },
                error : function(data, textStatus, xhr) {
                    $("#overlay").fadeOut();
                    if(data.responseJSON.error){
                        if(data.responseJSON.error.file_clock_in){
                            $('#file-error').text(data.responseJSON.error.file_clock_in[0])
                        }
                    }
                }
            });
            
        });
    });
    $(function() {
        $('#clock_out_btn').on('click', function(e){
            var fd = new FormData(document.querySelector('#clock_out'));    
            // fd.append( 'file', $('#anything_else_you_want_to_add') );					      
            $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
            let urlUpload = 'clockout';
            $.ajax({
                url: "{{url('/')}}/dashboard/"+urlUpload, 
                data: fd,
                processData: false,
                contentType: false,
                beforeSend:function () {
                                $("#overlay").fadeIn(300);
                },
                type: 'POST',
                success: function(data, textStatus, xhr){
                    $("#overlay").fadeOut();
                    toastr.success("Sukses, Clock Out Berhasil");
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                },
                error : function(data, textStatus, xhr) {
                    $("#overlay").fadeOut();
                    if(data.responseJSON.error){
                        if(data.responseJSON.error.file){
                            $('#file-error').text(data.responseJSON.error.file[0])
                        }
                    }
                }
            });
            
        }); 
    });
    $(function () {
        var table = $('.absensi-table').DataTable({
            processing: true,
            serverSide: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            ajax: "{{ route('absensi.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'stb', name: 'stb'},
                {data: 'name', name: 'name'},
                {data: 'clock_in', name: 'clock_in'},
                {data: 'file_clock_in', name: 'file_clock_in',
                    render:function(row, type, val, meta){
                        if(val.file_clock_in){
                            return "<img src=\"" + "/storage/{{config('app.documentImagePath')}}/absensi/"+val.file_clock_in+ "\" height=\"100\"/>";
                        }else{
                            return "-";
                        }
                    }
                },
                {data: 'clock_out', name: 'clock_out'},
                {data: 'file_clock_out', name: 'file_clock_out',
                    render:function(row, type, val, meta){
                        if(val.file_clock_out){
                            return "<img src=\"" + "/storage/{{config('app.documentImagePath')}}/absensi/"+val.file_clock_out+ "\" height=\"100\"/>";
                        }else{
                            return "-";
                        }
                    }
                },
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
    function refresh() {
        window.location.reload();
    }
    function deleteRecord(id, row_index) {
        let deleteUrl = 'grade/'+id;
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
                                let i = row_index.parentNode.parentNode.rowIndex;
                                let table = $('.absensi-table').DataTable();
                                table.draw();
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
