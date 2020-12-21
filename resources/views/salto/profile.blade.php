@extends('layouts.app')
@section('content')
<div class="container bootstrap snippet">
    <div class="card">
        <div class="card-body">
            <div class="row">
            </div>
            <div class="row">
                <div class="col-sm-3"><!--left col-->
                    <div class="container text-center">
                        <img src="http://ssl.gstatic.com/accounts/ui/avatar_2x.png" class="avatar img-circle img-thumbnail" alt="avatar">
                        <h6>Upload a different photo...</h6>
                        <input type="file" class="text-center center-block file-upload">
                    </div></hr><br>
                </div>
                <div class="col-sm-9">
                    <div class="container">
                        <form class="form" action="##" method="post" id="registrationForm">
                            <div class="form-group">
                                <div class="col-xs-6">
                                    <label for="first_name">Nama</label>
                                    <input type="text" class="form-control" name="nama" id="nama" placeholder="first name" title="masukan nama anda">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-6">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="enter phone" title="enter your phone number if any.">
                                </div>
                            </div>
                
                            <div class="form-group">
                                <div class="col-xs-6">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" class="form-control" name="mobile" id="mobile" placeholder="enter mobile number" title="enter your mobile number if any.">
                                </div>
                            </div>
                            <div class="form-group">
                                
                                <div class="col-xs-6">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="you@email.com" title="enter your email.">
                                </div>
                            </div>
                            <div class="form-group">
                                
                                <div class="col-xs-6">
                                    <label for="email">Location</label>
                                    <input type="email" class="form-control" id="location" placeholder="somewhere" title="enter a location">
                                </div>
                            </div>
                            <div class="form-group">
                                
                                <div class="col-xs-6">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="password" title="enter your password.">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-6">
                                    <label for="password2">Verify</label>
                                    <input type="password" class="form-control" name="password2" id="password2" placeholder="password2" title="enter your password2.">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                        <br>
                                        <button class="btn btn-lg btn-success" type="submit"><i class="glyphicon glyphicon-ok-sign"></i> Save</button>
                                        <button class="btn btn-lg" type="reset"><i class="glyphicon glyphicon-repeat"></i> Reset</button>
                                    </div>
                            </div>
                        </form>
                        <hr>  
                    </div><!--/tab-content-->
                </div><!--/col-9-->
            </div><!--/row-->
        </div>
    </div>
@endsection