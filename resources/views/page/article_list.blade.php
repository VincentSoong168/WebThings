@extends('layouts.app')

@section('title', 'Article List')

@section('css')
	<style type="text/css">
		.zoom-parent{
			height: 200px;
			overflow: hidden;
			padding-left: 0;
			padding-right: 0;
			border-right: transparent 15px solid;
			border-left: transparent 15px solid;
			position: relative;
			margin-bottom: 20px;
		}
		.zoom-child{
			width: 100%;
			height: 100%;
			background-color: black; /* fallback color */
			background-position: center;
			background-size: 100% 100%;
			transition: all .5s;
		}
		.zoom-child:hover{
		    transform: scale(1.2);
		}
		.zoom-board:hover + .zoom-child{
			transform: scale(1.2);
		}
		.zoom-board{
			position: absolute;
			z-index: 10;
			bottom: 0;
			left: 0;
			padding: 5px 15px;
			width: 100%;
			height: 40%;
			background-color: rgba(0,0,0,0.5);
			color: white;
		}
		.zoom-board>.btn{
			position: absolute;
			z-index: 15;
			right: 15px;
			bottom: 15px;
		}
	</style>
@endsection

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
	@if(session('fail'))
    <div class="alert alert-danger">
        <strong>{{ session('fail') }}</strong> 
    </div>
    @endif{{-- flash session after fail slug url --}}

    @if(sizeof($article_list)!=0)
		@foreach( $article_list as $article )
		<div class="col-md-6 zoom-parent">
			<div class="zoom-board">
				<strong>{{ $article->title }}</strong>
				<p>
					{{ $article->category['name'] or '尚未指定文章類別' }}
					<br>
					{{ $article->description }}
				</p>
				<a href="{{ route('page.single.article', $article->slug) }}" class="btn btn-success" role="button">Info</a>
			</div>
			<div class="zoom-child" style="background-image: url('{{ asset('storage/'.$article->image) }}');"></div>
		</div>
		@endforeach
	@else
		<div class="alert alert-danger">
	        <strong>沒有符合的結果 <a href="{{ route('page.article.list') }}" class="alert-link">點此返回文章列表</a></strong> 
	    </div>
	@endif
@endsection