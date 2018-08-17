@extends('layouts.app')

@section('title', 'Cart')

@section('css')
	<style type="text/css">
		th, td {
        	text-align: center;
            vertical-align: middle !important;
        }
        .panel-footer{
        	overflow: auto;
        }
	</style>
@endsection

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
	@if(session('fail'))
	    <div class="alert alert-danger">
	        <strong>{!! session('fail') !!}</strong> 
	    </div>
    @endif{{-- flash session after create or update --}}

	@if(session('success'))
	    <div class="alert alert-success">
	        <strong>{!! session('success') !!}</strong> 
	    </div>
    @endif{{-- flash session after create or update --}}
    
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="col-sm-10">
				<span>購物車</span>
			</div>
			<div class="col-sm-2">
				<span>總價 : {{ $total_price }}</span>
			</div>
		</div>

		<div class="panel-body">
			<table id="article_table" class="table table-hover">
				<thead>
					<tr>
						<td>商品名</td>
						<td>圖片</td>
						<td>數量</td>
						<td>單價</td>
						<td>小記</td>
						<td colspan="2">
							調整數量
						</td>
					</tr>
				</thead>
				<tbody>
					@foreach($product_list as $product)
					<tr>
						<td>{{ $product['collection']->name }}</td>
						<td>
							<img src="{{ asset('storage/'.$product['collection']->image) }}" width="125" height="175">
						</td>
						<td>{{ $product['quantity'] }}</td>
						<td>{{ $product['collection']->price }}</td>
						<td>{!! $product['quantity']*$product['collection']->price !!}</td>
						<td><a href="{{ route('cart.add', $product['collection']->id) }}" class="btn btn-block btn-success">增加</a></td>
						<td><a href="{{ route('cart.remove', $product['collection']->id) }}" class="btn btn-block btn-danger">減少</a></td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<div class="panel-footer">
			<div class="col-sm-3 col-sm-offset-3">
				<a href="{{ route('cart.pay') }}" class="btn btn-block btn-success">結帳</a>
			</div>
			<div class="col-sm-3">
				<a href="{{ route('cart.destroy.all') }}" class="btn btn-block btn-danger">清除</a>
			</div>
		</div>
	</div>
@endsection