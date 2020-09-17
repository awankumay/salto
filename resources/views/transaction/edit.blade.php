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
                        <b>Narapidana</b><br> {{$transactionHeader->convict_name}}
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
                                <div class="form-group col-md-12" ng-controller="SelectFileController">
                                    <strong>Foto</strong>
                                    <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                                    <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:200px" /></div>
                                    @if($transactionHeader->photo)<div ng-if="PreviewImage == null"> <img src="{{URL::to('/')}}/storage/{{config('app.productImagePath')}}/{{$transactionHeader->photo}}" class="img img-fluid" style="width:200px;height:200px;"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$transactionHeader->photo}}', '{{$transactionHeader->id}}', 'image')"> x </span> </div>@endif
                                    <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Ukuran foto < 300kb *'}}
                                    </span>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Tanggal Bayar:</strong>
                                    <br>
                                    <input type="date" class="form-control form-control-sm" name="date_payment" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <button type="submit" class="btn btn-success btn-sm">Upload</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 offset-lg-2">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <strong>Note</strong>
                                    <textarea name="note" rows="6" class="col-md-12"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <strong>Status:</strong>

                                    @if($transactionHeader->status==1)
                                    <font color="red">Pending</font>
                                    @else
                                    Sudah Bayar
                                    @endif
                                </div>
                                @if($transactionHeader->status==1)
                                <div class="form-group col-md-6">
                                    <button type="submit" class="btn btn-success btn-sm">Ubah Catatan</button>
                                </div>
                                @endif
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
                            <button type="button" onclick="updateItemBelanja({{$item->id}})" class="btn btn-info btn-sm"><i class='fas fa-edit'></i></button>
                            <button type="button" onclick="deleteItemBelanja({{$item->id}})" class="btn btn-danger btn-sm"><i class='fas fa-trash'></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-body">
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
function deleteExist(fileName, id, type) {
    let deleteUrl = 'deleteExistImageProduct';
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
$(document).ready(function() {
});
</script>
@endpush
@endsection
