@extends('layouts.app')

@section('title', 'Cart_Detail')

@section('css')
	<style type="text/css">
		th, td {
        	text-align: center;
            vertical-align: middle !important;
        }
	</style>
@endsection

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
    
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="col-md-4">
				<h5>訂單建立時間 : {{ $order->created_at }}</h5>
			</div>
			<div class="col-md-4 col-md-offset-4 text-right">
				<h5>訂單狀態 : {{ $status_array[$order->status] }}</h5>
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
					</tr>
				</thead>
				<tbody>
					@foreach(json_decode($order->list) as $product)
					<tr>
						<td>{{ $product->collection->name }}</td>
						<td>
							<img src="{{ asset('storage/'.$product->collection->image) }}" width="125" height="175">
						</td>
						<td>{{ $product->quantity }}</td>
						<td>{{ $product->collection->price }}</td>
						<td>{!! $product->quantity*$product->collection->price !!}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>

		<div class="panel-footer text-center">
			<h5>總價 : <strong class="text-danger">{{ $order->price }}</strong></h5>
		</div>
	</div>{{-- end of panel --}}

	<div class="col-md-3 col-md-offset-3 text-center">
		<a target="_blank" href="{{ route('cart.detail.pdf', $order->id) }}" class="btn btn-primary btn-block">建立PDF</a>
	</div>
	<div class="col-md-3 text-center">
		<a href="{{ route('cart.list') }}" class="btn btn-primary btn-block">返回訂單列表</a>
	</div>
@endsection