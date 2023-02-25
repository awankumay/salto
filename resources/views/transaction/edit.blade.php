@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('transaction.edit', $transactionHeader) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2"> Detail Transaksi {{$transactionHeader->id}}<br><br>
                    <b>Tanggal Transaksi</b> {{date_format($transactionHeader->created_at, 'd-F-Y H:i:s')}}</div>
                <div class="p-2">
                    <a class="btn btn-sm btn-success float-right" href="{{route('transaction.index')}}">Kembali</a></div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-lg-12 col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-lg-3">
                        <b>Pengguna</b><br> {{$transactionHeader->userapp}}
                    </div>
                    <div class="col-lg-3">
                        <b>Narapidana</b><br> {{$transactionHeader->convict_name}}
                    </div>
                    <div class="col-lg-3">
                        <b>Total Qty</b><br> {{$transactionHeader->tqty}}
                    </div>
                    <div class="col-lg-3">
                        <b>Total Harga</b><br>Rp. {{number_format($transactionHeader->tprice,0,",",".")}}
                    </div>
                </div>
            </div>
            <br>
            @if(!empty($transactionHeader->visitor_name))
            <div class="col-lg-12 col-sm-12 col-md-12">
                <div class="row">
                    <div class="col-lg-3">
                        <b>Pengunjung</b><br> {{$transactionHeader->visitor_name}}
                    </div>
                    <div class="col-lg-3">
                        <b>ID Kunjungan</b><br> {{$transactionHeader->id_visit}}
                    </div>
                    <div class="col-lg-3">
                        <b>Tanggal Berkunjung</b><br>
                        @php
                         if($transactionHeader->date_visit!=''){
                            $createVisitDate=date_create($transactionHeader->date_visit);
                            echo date_format($createVisitDate, 'd-F-Y');
                         }
                        @endphp
                    </div>
                </div>
            </div>
            @endif
            <br><br>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="row">
                                {!! Form::model($transactionHeader, ['method' => 'PATCH', 'route' => ['payment.update', $transactionHeader->id], 'enctype' => 'multipart/form-data']) !!}
                                <div class="form-group col-md-12" ng-controller="SelectFileController">
                                    <strong>Bukti Pembayaran</strong>
                                    <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                                    <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:200px" /></div>
                                    @if($transactionHeader->photo)<div ng-if="PreviewImage == null"> <img src="{{URL::to('/')}}/storage/{{config('app.documentImagePath')}}/{{$transactionHeader->photo}}" class="img img-fluid" style="width:200px;height:200px;"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$transactionHeader->photo}}', '{{$transactionHeader->id}}', 'image')"> x </span> </div>@endif
                                    @if($transactionHeader->photo)<a href="{{URL::to('/')}}/storage/{{config('app.documentImagePath')}}/{{$transactionHeader->photo}}" target='_blank'>Lihat File</a>@endif
                                    <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Ukuran foto < 300kb *'}}
                                    </span>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Tanggal Bayar:</strong>
                                    <br>
                                    {!! Form::date('date_payment', null, array('id'=>'date_payment', 'class' => 'form-control form-control-sm', 'required')) !!}
                                    <span class="form-text {{isset($errors->messages()['date_payment']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['date_payment']) ? $errors->messages()['date_payment'][0] .'*' : ''}}
                                </div>
                                <div class="form-group col-md-6">
                                    <button type="submit" class="btn btn-success btn-sm">Upload</button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <div class="col-lg-6 offset-lg-2">
                            <div class="row">
                                {!! Form::model($transactionHeader, ['method' => 'PATCH', 'route' => ['transaction.update', $transactionHeader->id], 'enctype' => 'multipart/form-data']) !!}
                                <div class="form-group col-md-12">
                                    <strong>Note</strong>
                                    <textarea name="note" class="form-control form-control-sm" rows="4" class="col-md-12">{{$transactionHeader->note_transaction}}</textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="1" @if($transactionHeader->status==1) selected @endif>Pending</option>
                                        <option value="2" @if($transactionHeader->status==2) selected @endif>Menunggu Pembayaran</option>
                                        <option value="4" @if($transactionHeader->status==4) selected @endif>Dibatalkan</option>
                                        @if($transactionHeader->status==3)
                                        <option value="3" selected>Lunas</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <button type="submit" class="btn btn-success btn-sm">Simpan Perubahan</button>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="card-body">
        </div>
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2"> Daftar Belanja</div>
            </div>
        </div>
        <div class="card-body">
            <br>
            <div class="col-lg-12 col-sm-12 col-md-12">
                @foreach ($detailTransaction as $item)
                    <div class="row" style="font-size: 14px;">
                        <div class="col-lg-3">
                            <b>Nama Produk</b><br> {{$item->product_name}}
                        </div>
                        <div class="col-lg-2">
                            <b>Qty</b><br> {{$item->qty_item}}
                        </div>
                        <div class="col-lg-2">
                            <b>Harga</b><br> {{number_format($item->price,0,",",".")}}
                        </div>
                        <div class="col-lg-2">
                            <b>Total</b><br> {{number_format($item->price*$item->qty_item,0,",",".")}}
                        </div>
                        <div class="col-lg-1" style="white-space: nowrap;">
                            <b></b><br>
                        <button type="button" data-toggle="modal" data-id="{{$item->id}}" data-item="{{$item->qty_item}}" data-produk="{{$item->product_name}}" data-target="#updatedQty" class="btn btn-info btn-sm"><i class='fas fa-edit'></i></button>
                            <button type="button" onclick="deleteItem({{$item->id}})" class="btn btn-danger btn-sm"><i class='fas fa-trash'></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-body">
        </div>
    </div>
</div>
<div class="modal fade" id="updatedQty" tabindex="-1" role="dialog" aria-labelledby="updatedQtyLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updatedQtyLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            Qty
            <input type="number" name="qty" id="qtyUpdated" class="form-control form-control-sm" value="">
            <input type="hidden" name="id" id="idUpdated" class="form-control form-control-sm" value="">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success btn-sm" onclick="updateQty()">Save changes</button>
        </div>
      </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
function deleteExist(fileName, id, type) {
    let deleteUrl = 'deleteExistImagePayment';
    let token ="{{csrf_token()}}";
    if(type=='image'){
        var params = {
            'image':fileName, 'id':id, "_token": token
        }
    }else{
        var params = {
            'document':fileName, 'id':id, "_token": token
        }
    }

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
                    url: "{{url('/')}}/dashboard/"+deleteUrl,
                    type: 'POST',
                    data: params,
                    success:function(){
                        window.location.reload(true);
                    },
                    error:function(){
                        setTimeout(function(){
                            $("#overlay").fadeOut(300);
                            toastr.error("Error, image deleted failure");
                        },500);
                    }
                });
            } else {
                //swal("Your imaginary file is safe!");
            }
    });
}

$('#updatedQty').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) // Button that triggered the modal
  var getQty = button.data('item')
  var getProduk = button.data('produk')
  var getId = button.data('id')
  var modal = $(this)
  modal.find('.modal-title').text('Edit ' + getProduk);
  modal.find('.modal-body #qtyUpdated').val(getQty)
  modal.find('.modal-body #idUpdated').val(getId)
})

function updateQty(){
    var getIdItem = $('#idUpdated').val();
    var getQtyItem = $('#qtyUpdated').val();
    let token ="{{csrf_token()}}";
    $('#updatedQty').modal('hide')
    $(document).ajaxSend(function() {
        $("#overlay").fadeIn(300);　
    });
    $.ajax({
        url: "{{url('/')}}/dashboard/updatedItem",
        type: 'POST',
        data: {
        "_token": token,
        "id":getIdItem,
        "qty":getQtyItem
        },
        success:function(){
            setTimeout(function(){
                $("#overlay").fadeOut(300);
                toastr.success("Berhasil, item berhasil diubah");
            },500);
            window.location.reload();
        },
        error:function(){
            setTimeout(function(){
                $("#overlay").fadeOut(300);
                toastr.error("Gagal, item tidak berhasil diubah");
            },500);
        }
    });

}

function deleteItem(id) {
        let deleteUrl = 'deleteItem';
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
                            url: "{{url('/')}}/dashboard/"+deleteUrl,
                            type: 'POST',
                            data: {
                            "_token": token,
                            "id":id
                            },
                            success:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.success("Berhasil, item berhasil dihapus");
			                    },500);
                                window.location.reload();
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Gagal, item tidak berhasil dihapus");
			                    },500);
                            }
                        });
                    } else {
                        //swal("Your imaginary file is safe!");
                    }
            });
    }

$(document).ready(function() {
});
</script>
@endpush
@endsection
