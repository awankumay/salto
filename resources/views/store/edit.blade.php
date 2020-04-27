@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('store.edit', $store) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Edit Store</div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($store, ['method' => 'PATCH','route' => ['store.update', $store->id]]) !!}
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Name:</strong>
                        {!! Form::text('store_name', null, array('placeholder' => 'Store name','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['store_name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['store_name']) ? $errors->messages()['store_name'][0] .'*' : 'Please input store name *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Phone Number:</strong>
                        {!! Form::text('phone_number', null, array('placeholder' => 'Phone number','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['phone_number']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['phone_number']) ? $errors->messages()['phone_number'][0] .'*' : 'Please input phone number *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Address:</strong>
                        {!! Form::textarea('address', null, array('rows' => 4, 'cols' => 54, 'class'=>'form-control form-control-sm', 'placeholder'=>'Store address')) !!}
                        <span class="form-text {{isset($errors->messages()['address']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['address']) ? $errors->messages()['address'][0] .'*' : 'you can skip this address'}}
                        </span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <button type="submit" class="btn btn-sm btn-success">Save</button>
                            <a class="btn btn-sm btn-success" href="{{route('store.index')}}">Cancel</a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
