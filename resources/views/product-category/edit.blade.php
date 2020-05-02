@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('product-category.edit', $productCategory) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Edit Product Category</div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($productCategory, ['method' => 'PATCH','route' => ['product-category.update', $productCategory->id], 'enctype' => 'multipart/form-data']) !!}
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Please input product category name *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Image:</strong>
                        {!! Form::file('file', null, array('placeholder' => 'Product category image','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Please input product category image *'}}
                        </span>
                        @if($productCategory->product_category_image)<img src="{{URL::to('/')}}/storage/{{config('app.productCategoryImagePath')}}/{{$productCategory->product_category_image}}" height="50"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$productCategory->product_category_image}}', '{{$productCategory->id}}')">x</span> @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Description:</strong>
                        {!! Form::textarea('description', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm', 'placeholder'=>'Description product category')) !!}
                        <span class="form-text {{isset($errors->messages()['description']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['description']) ? $errors->messages()['description'][0] .'*' : 'you can skip this description'}}
                        </span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                            <a class="btn btn-sm btn-success" href="{{route('product-category.index')}}">Cancel</a>
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
        let deleteUrl = 'deleteExistImageProductCategory';
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
