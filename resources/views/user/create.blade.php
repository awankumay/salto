@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('user.create') }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Add New User</div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::open(array('route' => ['user.store'],'method'=>'POST')) !!}
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['name']) ? $errors->messages()['name'][0] .'*' : 'Please input user name *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Email:</strong>
                        {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['email']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['email']) ? $errors->messages()['email'][0] .'*' : 'Please input user email *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Password:</strong>
                        {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['password']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['password']) ? $errors->messages()['password'][0] .'*' : 'Please input user password *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Confirm Password:</strong>
                        {!! Form::password('confirm-password', array('placeholder' => 'Confirm Password','class' => 'form-control form-control-sm')) !!}
                        <span class="form-text {{isset($errors->messages()['password']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['password']) ? $errors->messages()['password'][0] .'*' : 'Please confirm user password *'}}
                        </span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group col-md-4">
                        <strong>Role:</strong>
                        {!! Form::select('role[]', $role, [], array('class' => 'form-control','single', 'placeholder'=>'Please select role')) !!}
                        <span class="form-text {{isset($errors->messages()['role']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['role']) ? $errors->messages()['role'][0] .'*' : 'Please select user role *'}}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-sm-10 col-md-10"></div>
            <div class="col-xs-12 col-sm-12 col-md-6">
                    <button type="submit" class="btn btn-sm btn-success">Save</button>
                    <a class="btn btn-sm btn-success" href="{{route('user.index')}}">Cancel</a>
            </div>
        </div>
            {!! Form::close() !!}
    </div>
</div>
@endsection
