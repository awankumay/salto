@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('role.edit', $role) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Edit New Role</div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($role, ['method' => 'PATCH','route' => ['role.update', $role->id]]) !!}
            {{ csrf_field() }}
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control form-control-sm')) !!}
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>Permission:</strong>
                        <br/>
                        @foreach($permission as $value)
                            <label>{{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}
                            {{ $value->name }}</label>
                        <br/>
                        @endforeach
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6">
                    <button type="submit" class="btn btn-sm btn-danger">Save</button>
                    <a class="btn btn-sm btn-warning" href="{{route('role.index')}}">Cancel</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
