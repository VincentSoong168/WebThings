@extends('layouts.app')

@section('title', 'Product List')

@section('css')
	<style type="text/css">
		dl{
			margin-top: 20px;
		}
		.description{
			min-height: 90px;
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

    @if(session('success'))
    <div class="alert alert-success">
        <strong>{!! session('success') !!}</strong> 
    </div>
    @endif{{-- flash session after cart add success --}}

    @if(sizeof($product_list)!=0)
		@foreach( $product_list as $product )
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{{ $product->name }}</h3>
				</div>
				<div class="panel-body">
					<div class="text-center">
						<img src="{{ asset('storage/'.$product->image) }}">
					</div>
					<dl class="description">
						<dt>書籍簡介 : </dt>
						<dd>
							@if( strlen($product->description)>140)
								{{ substr($product->description, 0, 140).'...' }}
							@else
								{{ $product->description }}
							@endif
						</dd>
					</dl>
					<dl>
						<dt>價格 : </dt>
						<dd>{{ $product->price }}</dd>
					</dl>
					<div class="text-center">
						<a href="{{ route('cart.add', $product->id) }}" class="btn btn-success">加入購物車</a>
					</div>
				</div>
			</div>
		</div>
		@endforeach
	@else
		<div class="alert alert-danger">
	        <strong>產品尚待新增</strong> 
	    </div>
	@endif

	@if($product_list->lastPage()>1)
		<div class="col-md-12">
			{{ $product_list->links() }}
		</div>
	@endif
@endsection