@extends('layouts.app')

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
    @if(session('success')){{-- flash session after create or update --}}
    <div class="alert alert-success">
        <strong>{{ session('success') }}</strong>
    </div>
    @endif

    <div class="panel panel-primary">
        <div class="panel-heading" style="overflow: auto;">
            <h3 class="panel-title">{{ $article->title }}</h3>
            <hr>

            <h5 class="pull-left">更新日期 : {{ date("Y年m月d日 H:i:s", strtotime($article->updated_at)) }}</h5>
            <a href="{{ route('article.edit', $id) }}" class="btn btn-success pull-right" role="button" style="width: 70px;">Edit</a>

            <div class="clearfix"></div>

            <h5 class="pull-left">建立日期 : {{ date('Y年m月d日 H:i:s', strtotime($article->created_at)) }}</h5>
            <a href="/" class="btn btn-danger pull-right" role="button" style="width: 70px;">Delete</a>
        </div>
        <div class="panel-body">
            {!! $article->content !!}
        </div>
    </div>
@endsection