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
				<span>產品列表</span>
			</div>
			<div class="col-sm-1">
				<a href="{{ route('product.create') }}" class="btn btn-success" role="button" style="width: 70px;">Create</a>
			</div>
		</div>

		<table id="product_table" class="table table-hover">
			<thead>
				<tr>
					<td>排序</td>
					<td>商品名稱</td>
					<td>圖片</td>
					<td>價格</td>
					<td>更新時間</td>
					<td>創建時間</td>
					<td colspan="2">檢視</td>
				</tr>
			</thead>
			<tbody>
				@if(sizeof($product_list)>0)
					@foreach($product_list as $key => $product)
					<tr>
						<td>{{ ($product_list->currentPage()-1)*$product_list->perPage()+$key+1 }}</td>
						<td>{{ $product->name }}</td>
						<td><img src="{{ asset('storage/'.$product->image) }}" width="125" height="175"></td>
						<td>{{ $product->price }}</td>
						<td>{{ $product->created_at }}</td>
						<td>{{ $product->updated_at }}</td>
						<td>
							<a href="{{ route('product.edit', $product->id) }}" class="btn btn-success" role="button" style="width: 70px;">Edit</a>
						</td>
						@if(Auth::check())
						<td>
							<form action="{{ route('product.destroy', $product->id) }}" method="post" onsubmit="return confirm('確認刪除?');">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="btn btn-danger">Delete</button>
							</form>
						</td>
						@endif
					</tr>
					@endforeach
				@else
					<tr>
						<td colspan="8">暫無資料</td>
					</tr>
				@endif
			</tbody>
		</table>
		@if($product_list->lastPage()>1)
		<div class="panel-footer panel-primary">
			{{ $product_list->links() }}
		</div>
		@endif
	</div>
@endsection