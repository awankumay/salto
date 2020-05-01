@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('product') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Table Product</div>
                <div class="p-2">
                    @if(auth()->user()->hasPermissionTo('product-create'))
                        <a href="{{route('product.create')}}" class="btn btn-success btn-sm text-white btn-add">Add Product</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table table-responsive">
                <table class="table table-responsive table-hover table-bordered product-table">
                    <thead>
                        <tr>
                            <th style="width:5%;">No</th>
                            <th style="width:15%;">Product Code</th>
                            <th style="width:20%;">Image</th>
                            <th style="width:20%;">Name</th>
                            <th style="width:5%;">Category</th>
                            <th>Type</th>
                            <th style="width:25%;">Description</th>
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
        let table = $('.product-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('product.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'product_code', name: 'product_code'},
                {data: 'product_image',
                 name: 'product_image',
                    render:function(row, type, val, meta){
                        if(val.product_image==null){
                            if(val.product_category['0'].id==1){
                                return "<img src=\"" + "food.png"+"\" height=\"50\"/>";
                            }else{
                                return "<img src=\"" + "drink.png"+"\" height=\"50\"/>";
                            }
                        }else{
                            return "<img src=\"" + "storage/{{config('app.productImagePath')}}/"+val.product_image+ "\" height=\"50\"/>";
                        }
                    },
                orderable: false,
                searchable: false},
                {data: 'product_name', name: 'product_name'},
                {data: 'product_category.0.name', name: 'product_category'},
                {data: 'product_sale', name: 'product_sale',
                    render:function(data) {
                        if(data==1){
                            return 'Saleable';
                        }else{
                            return 'Herbs';
                        }
                    }
                },
                {data: 'product_description', name: 'description'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });

    function deleteRecord(id, row_index) {
        let deleteUrl = 'product/'+id;
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
                                    toastr.success("Success, product deleted successfully");
			                    },500);
                                let i = row_index.parentNode.parentNode.rowIndex;
                                let table = $('.product-table').DataTable();
                                table.row(i).remove().draw();
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Error, product deleted successfully");
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
