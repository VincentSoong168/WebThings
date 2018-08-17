@extends('layouts.app')

@section('title', 'Edit Product : '.$product->title)

@section('css')
    <link type="text/css" rel="stylesheet" href="{{asset('css/parsley.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/cropper/cropper.min.css')}}" type="text/css" />
    <style type="text/css">
        img {
          max-width: 100%; /* This rule is very important, please do not ignore this! */
        }
    </style>
@endsection

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
    @if($errors->any())
        @foreach($errors->all() as $errors)
            <div class="alert alert-danger">
                <strong>{{ $errors }}</strong> 
            </div>
        @endforeach
    @endif

    <form id="product_form" class="form-horizontal" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data" method="POST" data-parsley-validate>
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Product Title</label>
            <div class="col-sm-10">
                <input value="{{ old( 'name', $product->name) }}" type="text" class="form-control" id="name" name="name" maxlength="255" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="description">Product Description</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="description" name="description">{{ old( 'description', $product->description) }}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="price">Product Price</label>
            <div class="col-sm-10">
                <input value="{{ old( 'price', $product->price) }}" type="number" class="form-control" id="price" name="price" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="image">Product Image</label>
            <div class="col-sm-10">
                <input type="file" id="image" name="image">

                <input type="hidden" class="form-control" id="x" name="x">
                <input type="hidden" class="form-control" id="y" name="y">
                <input type="hidden" class="form-control" id="width" name="width">
                <input type="hidden" class="form-control" id="height" name="height">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                <img id="image_preview" src="{{ asset('storage/'.$product->image) }}">
            </div>
        </div>

        <div class="col-sm-6 col-sm-offset-3">
            <button type="submit" class="btn btn-success btn-block btn-default">Submit</button>
        </div>
    </form>
@endsection

@section('javascript')
    <script src="{{ asset('js/parsley.min.js') }}"></script>
    <script src="{{asset('js/cropper/cropper.js')}}"></script>
    <script src="{{asset('js/cropper/jquery-cropper.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            var $image;

            $("#image").on("change", function(){
                readURL(this);

                $image = $('#image_preview');

                $image.cropper('destroy');

                $image.cropper({
                  //aspectRatio: {{ $resize_width }} / {{ $resize_height }},
                  viewMode: 1,
                  guides: false,
                  zoomable: false,
                  mouseWheelZoom: false,
                  rotatable: false,
                  background: false,
                  crop: function(event) {
                    $("#x").val(event.detail.x);
                    $("#y").val(event.detail.y);
                    $("#width").val(event.detail.width);
                    $("#height").val(event.detail.height);
                  }
                });

                // Get the Cropper.js instance after initialized
                // var cropper = $image.data('cropper');
            });
        });

        function readURL(input) {
            if (input.files && input.files[0]) {

                imageUrl = window.URL.createObjectURL(input.files[0]);

                $('#image_preview').attr('src', imageUrl);
            }
        }
    </script>
@endsection