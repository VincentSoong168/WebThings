@extends('layouts.app')

@section('title', 'Product Panel')

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
	@if(session('success'))
    <div class="alert alert-success">
        <strong>
        	{{ session('success') }}
        	@if(session('link_text')&&session('link_url'))
        	<a href="{{ session('link_url') }}" class="alert-link">
        		{{ session('link_text') }}
        	</a>
        	@endif
        </strong> 
    </div>
    @endif{{-- flash session after create or update --}}
    
	<div class="panel panel-primary">
		<div class="panel-heading">
			<div class="col-sm-11">
				<span>訂單一覽</span>
			</div>
		</div>

		<table id="product_table" class="table table-hover">
			<thead>
				<tr>
					<td>#</td>
					<td>訂單編號</td>
					{{-- <td>訂單內容</td> --}}
					<td>總價</td>
					<td>建立時間</td>
					<td>目前狀態</td>
					<td>檢視</td>
				</tr>
			</thead>
			<tbody>
				@forelse($order_list as $order)
					<tr>
						<td>{{ $order_list->perPage()*($order_list->currentPage()-1)+$loop->iteration }}</td>
						<td>{{ $order->order_no }}</td>
						{{-- <td>
							@foreach( json_decode($order->list) as $product )
								<h5>{{ $product->collection->name }} X {{ $product->quantity }}</h5>
							@endforeach
						</td> --}}
						<td>{{ $order->price }}</td>
						<td>{{ $order->created_at }}</td>
						<td>{{ $status_array[$order->status] }}</td>
						<td>
							<a href="{{ route('cart.detail', $order->id) }}" class="btn btn-block btn-primary">檢視</a>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="6">目前尚未建立任何訂單</td>
					</tr>
				@endforelse
			</tbody>
		</table>
		@if($order_list->lastPage()>1)
		<div class="panel-footer panel-primary">
			{{ $order_list->links() }}
		</div>
		@endif
	</div>
@endsection