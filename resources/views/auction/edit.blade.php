@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between">
        {{ Breadcrumbs::render('auction.edit', $auction) }}
    </div>
    <div class="card table col-md-12 px-1 py-1" style="background-color: #fdfdfd !important;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="p-2">Ubah Auction</div>
                <div class="p-2">
                    <a class="btn btn-sm btn-success float-right" href="{{route('auction.index')}}">Kembali</a></div>
            </div>
        </div>
        <div class="card-body">
            {!! Form::model($auction, ['method' => 'PATCH', 'route' => ['auction.update', $auction->id], 'enctype' => 'multipart/form-data']) !!}
            <div class="row">
                <div class="col-lg-8 col-sm-12 col-md-12">
                    <div class="form-group col-md-12">
                        <strong>Judul  </strong>
                        {!! Form::text('title', null, array('placeholder' => 'Judul', 'ng-trim'=>'false', 'maxlength'=>'100', 'id'=>'title', 'class' => 'form-control form-control-sm editable')) !!}
                        <span class="form-text {{isset($errors->messages()['title']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['title']) ? $errors->messages()['title'][0] .'*' : 'Judul wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Ringkasan:</strong>
                        {!! Form::textarea('excerpt', null, array('rows' => 3, 'maxlength'=>'250', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Ringkasan')) !!}
                        <span class="form-text {{isset($errors->messages()['excerpt']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['excerpt']) ? $errors->messages()['excerpt'][0] .'*' : 'Ringkasan wajib disii *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Nama Produk:</strong>
                        {!! Form::text('product_name', null, array('placeholder' => 'Nama Produk', 'ng-trim'=>'false', 'maxlength'=>'100', 'id'=>'product_name', 'class' => 'form-control form-control-sm editable')) !!}
                        <span class="form-text {{isset($errors->messages()['product_name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['product_name']) ? $errors->messages()['product_name'][0] .'*' : 'Judul wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Produk Kategori:</strong>
                        {!! Form::select('product_categories_id[]', $product_categories, $selectProductCategories, array('class' => 'form-control form-control-sm product_category', 'single')) !!}
                        <span class="form-text {{isset($errors->messages()['product_categories_id']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['product_categories_id']) ? $errors->messages()['product_categories_id'][0] .'' : 'Pilih Produk Kategori * '}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Konten:</strong>
                      {!! Form::textarea('content', null, array('id'=>'summernote', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Konten')) !!}
                        <span class="form-text {{isset($errors->messages()['content']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['content']) ? $errors->messages()['content'][0] .'*' : 'Konten wajib disii *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <strong>Mulai:</strong><br>
                                {!! Form::date('date_started', null, array('id'=>'date_started', 'class' => 'form-control form-control-sm')) !!}
                                <span class="form-text {{isset($errors->messages()['date_started']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['date_started']) ? $errors->messages()['date_started'][0] .'*' : 'Tanggal mulai wajib diisi *'}}
                            </div>
                            <div class="form-group col-md-6">
                                <strong>Selesai:</strong><br>
                                {!! Form::date('date_ended', null, array('id'=>'date_ended', 'class' => 'form-control form-control-sm')) !!}
                                <span class="form-text {{isset($errors->messages()['date_ended']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['date_ended']) ? $errors->messages()['date_ended'][0] .'*' : 'Tanggal selesai wajib diisi *'}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <strong>Opsi Buy Now:</strong><br>
                                {!! Form::radio('buy_now', 0, array('class' => 'form-control form-control-sm buy_now')) !!} Tidak &nbsp;
                                {!! Form::radio('buy_now', 1, array('class' => 'form-control form-control-sm buy_now')) !!} Ya &nbsp;
                                <span class="form-text {{isset($errors->messages()['buy_now']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['buy_now']) ? $errors->messages()['buy_now'][0] .'*' : 'pilih salah satu *'}}
                                </span>
                            </div>
                            <div class="form-group col-md-6">
                                <strong>Harga Buy Now  </strong>
                                {!! Form::text('price_buy_now', $auction->price_buy_now ?? null, array('placeholder' => '', 'ng-trim'=>'false', 'id'=>'price_buy_now', 'class' => 'form-control form-control-sm')) !!}
                                {!! Form::hidden('price_buy_now_value', $auction->price_buy_now ?? null, array('placeholder' => '', 'ng-trim'=>'false', 'id'=>'price_buy_now_value', 'class' => 'form-control form-control-sm')) !!}
                                <span class="form-text {{isset($errors->messages()['price_buy_now_value']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                    {{isset($errors->messages()['price_buy_now_value']) ? $errors->messages()['price_buy_now_value'][0] .'*' : 'Harga buy now wajib diisi*'}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <strong>Open Bid  </strong>
                                {!! Form::text('open_bid', $auction->start_price ?? null, array('placeholder' => '', 'ng-trim'=>'false', 'id'=>'open_bid', 'class' => 'form-control form-control-sm')) !!}
                                {!! Form::hidden('start_price_value', $auction->start_price ?? null, array('placeholder' => '', 'ng-trim'=>'false', 'id'=>'start_price_value', 'class' => 'form-control form-control-sm')) !!}
                                <span class="form-text {{isset($errors->messages()['open_bid']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['open_bid']) ? $errors->messages()['open_bid'][0] .'*' : 'Open bid wajib diisi*'}}
                                </span>
                            </div>
                            <div class="form-group col-md-4">
                                <strong>Kelipatan bid  </strong>
                                {!! Form::text('multiple_bid', $auction->multiple_bid ?? null, array('placeholder' => '', 'ng-trim'=>'false', 'id'=>'multiple_bid', 'class' => 'form-control form-control-sm')) !!}
                                {!! Form::hidden('multiple_bid_value', $auction->multiple_bid ?? null, array('placeholder' => '', 'ng-trim'=>'false', 'id'=>'multiple_bid_value', 'class' => 'form-control form-control-sm')) !!}
                                <span class="form-text {{isset($errors->messages()['multiple_bid']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['multiple_bid']) ? $errors->messages()['multiple_bid'][0] .'*' : 'Kelipatan bid wajib diisi*'}}
                                </span>
                            </div>
                            <div class="form-group col-md-4">
                                <strong>Rate donasi dari bid</strong>
                                {!! Form::select('rate_donation[]', $donationRate, $selectRate, array('class' => 'form-control form-control-sm tagging', 'single', 'placeholder'=>'Pilih rate donasi')) !!}
                                <span class="form-text {{isset($errors->messages()['rate_donation']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['rate_donation']) ? $errors->messages()['rate_donation'][0] .'' : 'Pilih rate donasi * '}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="form-group col-md-12" ng-controller="SelectFileController">
                        <strong>Foto Utama</strong>
                        <input type="file" name="file" onchange="angular.element(this).scope().SelectFile(event)">
                        <div class="mt-1"><img ng-src="<%= PreviewImage %>" ng-if="PreviewImage != null" alt="" style="height:200px;width:200px" /></div>
                        @if($auction->photo)<div ng-if="PreviewImage == null"> <img src="{{URL::to('/')}}/storage/{{config('app.auctionImagePath')}}/{{$auction->photo}}" class="img img-fluid" style="width:200px;height:200px;"/><span style="cursor: pointer;color:red;" onclick="deleteExist('{{$auction->photo}}', '{{$auction->id}}')"> x </span> </div>@endif
                        <span class="form-text {{isset($errors->messages()['file']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['file']) ? $errors->messages()['file'][0] .'*' : 'Ukuran foto < 100kb *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Status:</strong><br>
                        {!! Form::radio('status', 1, array('class' => 'form-control form-control-sm')) !!} Aktif &nbsp;
                        {!! Form::radio('status', 2, array('class' => 'form-control form-control-sm')) !!} Tidak Aktif &nbsp;
                        <span class="form-text {{isset($errors->messages()['status']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['status']) ? $errors->messages()['status'][0] .'*' : 'Pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Headline:</strong><br>
                        {!! Form::radio('headline', 1, array('class' => 'form-control form-control-sm')) !!} Ya &nbsp;
                        {!! Form::radio('headline', 2, array('class' => 'form-control form-control-sm')) !!} Tidak &nbsp;
                        <span class="form-text {{isset($errors->messages()['headline']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['headline']) ? $errors->messages()['headline'][0] .'*' : 'pilih salah satu *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Tags:</strong>
                        {!! Form::select('tags[]', $tags, $selectTags, array('class' => 'form-control form-control-sm tagging', 'multiple')) !!}
                        <span class="form-text {{isset($errors->messages()['tags']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                            {{isset($errors->messages()['tags']) ? $errors->messages()['tags'][0] .'' : 'Pilih tags'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Meta Title</strong>
                        {!! Form::textarea('meta_title', null, array('rows' => 2, 'maxlength'=>'120', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Meta Title')) !!}
                        <span class="form-text {{isset($errors->messages()['meta_title']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['meta_title']) ? $errors->messages()['meta_title'][0] .'*' : 'Meta title wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <strong>Meta Deskripsi</strong>
                        {!! Form::textarea('meta_description', null, array('rows' => 4, 'maxlength'=>'200', 'class'=>'form-control form-control-sm editable', 'placeholder'=>'Meta Deskripsi')) !!}
                        <span class="form-text {{isset($errors->messages()['meta_description']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                        {{isset($errors->messages()['meta_description']) ? $errors->messages()['meta_description'][0] .'*' : 'Meta deskripsi wajib diisi *'}}
                        </span>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <strong>Pilih Akun penerima</strong><br>
                                {!! Form::select('beneficiary_account_issuer[]', $beneficiary_account_issuer, $selectBeneficiary, array('class' => 'form-control form-control-sm tagging', 'single', 'placeholder'=>'Pilih Akun')) !!}
                                <span class="form-text {{isset($errors->messages()['beneficiary_account_issuer']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['beneficiary_account_issuer']) ? $errors->messages()['beneficiary_account_issuer'][0] .'' : 'Pilih Akun * '}}
                                </span>
                            </div>
                            <div class="form-group col-md-12">
                                <strong>No.Akun Penerima</strong><br>
                                {!! Form::number('beneficiary_account', null, array('placeholder' => 'No Akun', 'ng-trim'=>'false', 'maxlength'=>'20', 'id'=>'beneficiary_account', 'class' => 'form-control form-control-sm editable')) !!}
                                <span class="form-text {{isset($errors->messages()['beneficiary_account']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['beneficiary_account']) ? $errors->messages()['beneficiary_account'][0] .'*' : 'No Rek wajib diisi *'}}
                                </span>
                            </div>
                            <div class="form-group col-md-12">
                                <strong>Nama Akun penerima</strong><br>
                                {!! Form::text('beneficiary_account_name', null, array('placeholder' => 'Nama Akun', 'ng-trim'=>'false', 'maxlength'=>'50', 'id'=>'beneficiary_account_name', 'class' => 'form-control form-control-sm editable')) !!}
                                <span class="form-text {{isset($errors->messages()['beneficiary_account_name']) ? 'text-danger text-help' : 'text-muted text-help'}}">
                                {{isset($errors->messages()['beneficiary_account_name']) ? $errors->messages()['beneficiary_account_name'][0] .'*' : 'Nama akun wajib diisi *'}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12">
                    <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <button type="submit" class="btn btn-sm btn-success">Simpan</button>
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
    let deleteUrl = 'deleteExistImageAuction';
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
    $('#price_buy_now').number( true, 0);
    $('#price_buy_now').on('keyup',function(){
        var priceBuy = $('#price_buy_now').val();
        $('#price_buy_now_value').val(priceBuy);
	});

    $('#open_bid').number( true, 0);
    $('#open_bid').on('keyup',function(){
        var openBid = $('#open_bid').val();
        $('#start_price_value').val(openBid);
	});

    $('#multiple_bid').number( true, 0);
    $('#multiple_bid').on('keyup',function(){
        var multipleBid = $('#multiple_bid').val();
        $('#multiple_bid_value').val(multipleBid);
	});
    var getBuyNow = $("input:radio[name='buy_now']:checked").val();
    if(getBuyNow==1){
        $("#price_buy_now").prop("disabled", false);
      /*   $('#price_buy_now_value').val('');
        $('#price_buy_now').val(''); */
    }else{
        $("#price_buy_now").prop("disabled", true);
        $('#price_buy_now_value').val(0);
        $('#price_buy_now').val(0);
    }

    $('input:radio[name="buy_now"]').change(
    function(){
        if (this.checked && this.value == '0') {
            $("#price_buy_now").prop("disabled", true);
            $('#price_buy_now_value').val(0);
            $('#price_buy_now').val(0);
        }else{
            $("#price_buy_now").prop("disabled", false);
 /*            $('#price_buy_now_value').val('');
            $('#price_buy_now').val(''); */
        }
    });

    $('.tagging').select2();
    $('.product_category').select2();
    setTimeout(() => {
        $('#summernote').summernote({
            toolbar:[
                ['cleaner',['cleaner']], // The Button
                ['style',['style']],
                ['font',['bold','italic','underline','clear']],
                ['fontname',['fontname']],
                ['color',['color']],
                ['para',['ul','ol','paragraph']],
                ['height',['height']],
                ['table',['table']],
                ['insert',['picture','link','hr']],
                ['view',['fullscreen','codeview']],
            ],
            cleaner:{
                action: 'both', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
                newline: '<br>', // Summernote's default is to use '<p><br></p>'
                notStyle: 'position:absolute;top:0;left:0;right:0', // Position of Notification
                icon: '<i class="note-icon">[Reset]</i>',
                keepHtml: false, // Remove all Html formats
                keepOnlyTags: ['<p>', '<br>', '<ul>', '<li>', '<b>', '<strong>','<i>', '<a>'], // If keepHtml is true, remove all tags except these
                keepClasses: false, // Remove Classes
                badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'], // Remove full tags with contents
                badAttributes: ['style', 'start'], // Remove attributes from remaining tags
                limitChars: false, // 0/false|# 0/false disables option
                limitDisplay: 'both', // text|html|both
                limitStop: false // true/false
            },
            height: 350,
            callbacks: {
                onImageUpload: function(image) {
                    var sizeKB = image[0]['size'] / 1000;
                    var tmp_pr = 0;
                    if(sizeKB > 100){
                        tmp_pr = 1;
                        toastr.error("Gagal, Ukuran gambar maksimal 100kb");
                    }
                    if(image[0]['type'] != 'image/jpeg' && image[0]['type'] != 'image/png'){
                        tmp_pr = 1;
                        toastr.error("Gagal, Tipe file yang diizinkan jpeg/png");
                    }
                    if(tmp_pr == 0){
                        var file = image[0];
                        var reader = new FileReader();
                        reader.onloadend = function() {
                            var image = $('<img>').attr('src',  reader.result);
                            $('#summernote').summernote("insertNode", image[0]);
                        }
                    reader.readAsDataURL(file);
                    }
                }
            }
        });
    }, 200);

});
</script>
@endpush
@endsection
