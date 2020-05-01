@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('product.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Add New Product</div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($product, ['method'=>'PATCH', 'route' => ['product.update', $product->id],  'enctype' => 'multipart/form-data']) !!}
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Name:</strong>
                        {!! Form::text('product_name', null, array('placeholder' => 'Product name','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['product_name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['product_name']) ? $errors->messages()['product_name'][0] .'*' : 'Please input product name *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Image:</strong>
                        {!! Form::file('file', null, array('placeholder' => 'Product image','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Product image size < 300kb *'}}
                        </span>
                    @if($product->product_image)<img src="{{URL::to('/')}}/storage/{{config('app.productImagePath')}}/{{$product->product_image}}" height="50"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$product->product_image}}', '{{$product->id}}')">x</span> @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Category:</strong>
                        {!! Form::select('product_category[]', $productCategory, $productCategoryPick, array('class' => 'form-control form-control-sm','single', 'placeholder'=>'Please select category')) !!}
                        <span class="form-text {{isset($errors->messages()['product_category']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['product_category']) ? $errors->messages()['product_category'][0] .'*' : 'Please select category *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Saleable:</strong><br>
                        {!! Form::radio('product_sale', 1, array('class' => 'form-control form-control-sm')) !!} Yes &nbsp;
                        {!! Form::radio('product_sale', 0, array('class' => 'form-control form-control-sm')) !!} No &nbsp;
                        <span class="form-text {{isset($errors->messages()['product_sale']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['product_sale']) ? $errors->messages()['product_sale'][0] .'*' : 'Select product type *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Cost:</strong>
                        {!! Form::number('product_cost', null, array('placeholder' => 'Product Cost','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['product_cost']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['product_cost']) ? $errors->messages()['product_cost'][0] .'*' : 'Please input product cost *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Description:</strong>
                        {!! Form::textarea('product_description', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm', 'placeholder'=>'Description')) !!}
                        <span class="form-text {{isset($errors->messages()['product_description']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['product_description']) ? $errors->messages()['product_description'][0] .'*' : 'you can skip this description'}}
                        </span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                            <a class="btn btn-sm btn-success" href="{{route('product.index')}}">Cancel</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
    function deleteExist(fileName, id) {
        let deleteUrl = 'deleteExistImageProduct';
        let token ="{{csrf_token()}}";
        let params = {
           'image':fileName, 'id':id, "_token": token,
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
                            url: "{{url('/')}}/"+deleteUrl,
                            type: 'POST',
                            data: params,
                            success:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.success("Success, image deleted successfully");
			                    },500);
                                window.location.reload(true);
                            },
                            error:function(){
                                setTimeout(function(){
				                    $("#overlay").fadeOut(300);
                                    toastr.error("Error, image deleted failure");
			                    },500);
                                window.location.reload(true);
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
