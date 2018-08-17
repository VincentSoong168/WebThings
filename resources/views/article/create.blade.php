@extends('layouts.app')

@section('title', 'Create Article')

@section('css')
    <link type="text/css" rel="stylesheet" href="{{asset('css/parsley.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/select2/select2.min.css')}}" type="text/css" />
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

    <form id="article_form" class="form-horizontal" action="{{ route('article.store') }}" enctype="multipart/form-data" method="POST" data-parsley-validate>
        {{ csrf_field() }}
        <div class="form-group">
            <label class="col-sm-2 control-label" for="title">Article Title</label>
            <div class="col-sm-10">
                <input value="{{ old('title') }}" type="text" class="form-control" id="title" name="title" maxlength="255" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="slug">Article Slug</label>
            <div class="col-sm-10">
                <input value="{{ old('slug') }}" type="text" class="form-control" id="slug" name="slug" maxlength="255" required>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="description">Article Description</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="content">Article Content</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="content" name="content">{{ old('content') }}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="image">Article Image</label>
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
                <img id="image_preview" src="">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="category_id">Article Category</label>
            <div class="col-sm-4">
                <select id="category_id" class="form-control" name="category_id">
                    @if(count($category_list)==0)
                        <option value="0">尚未建立文章分類</option>
                    @else
                        <option value="0">不使用文章分類</option>
                        <option disabled>---------------------</option>
                        @foreach($category_list as $category)
                            <optgroup label="{{ $category->name }}">
                                @forelse($category->children as $child)
                                    <option value="{{ $child['id'] }}">{{ $child['name'] }}</option>
                                @empty
                                    <option disabled>尚未建立子項目</option>
                                @endforelse
                            </optgroup>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="tag_id">Article Tag</label>
            <div class="col-sm-4">
                <select id="tag_id" class="form-control" name="tag_id[]" multiple="multiple">
                    @if(sizeof($tag_list)!=0)
                        @foreach($tag_list as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    @else
                        <option disabled>尚未建立項目</option>
                    @endif
                </select>
            </div>
        </div>

        <div class="form-group"> 
            <label class="col-sm-2 control-label">Article Status</label>
            <div class="col-sm-10">
                <label class="radio-inline">
                    <input type="radio" name="status" id="inlineRadio1" value="1" checked> 開放
                </label>
                <label class="radio-inline">
                    <input type="radio" name="status" id="inlineRadio2" value="0"> 關閉
                </label>
            </div>
        </div>

        <div class="col-sm-6 col-sm-offset-3">
            <button type="submit" class="btn btn-success btn-block btn-default">Submit</button>
        </div>
    </form>
@endsection

@section('javascript')
    <script src="https://cdn.ckeditor.com/4.9.2/standard/ckeditor.js"></script>
    <script src="{{ asset('js/parsley.min.js') }}"></script>
    <script src="{{asset('js/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('js/cropper/cropper.js')}}"></script>
    <script src="{{asset('js/cropper/jquery-cropper.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            CKEDITOR.replace('content');

            $("#tag_id").select2({
                closeOnSelect: false
            });

            var $image;

            $("#image").change(function() {
                readURL(this);

                $image = $('#image_preview');

                $image.cropper('destroy');

                $image.cropper({
                  aspectRatio: '{{ $resize_width }}' / '{{ $resize_height }}',
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
                var cropper = $image.data('cropper');
            });
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#image_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
                imageUrl = window.URL.createObjectURL(input.files[0])
                $('#image_preview').attr('src', imageUrl);
            }
        }
    </script>
@endsection