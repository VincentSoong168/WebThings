<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="utf-8">
	<title>Order PDF</title>
	<style>
		.head_line{
			text-align: center;
		}
		table {
		    border-collapse: collapse;
		    width: 90%;
		    margin: 0 auto;
		}

		tr {
			border: 1px solid black;
		}

		th, td {
		    text-align: center;
		    padding: 8px;
		}

		tr:nth-child(even){
			background-color: #f2f2f2;
		}

		th {
		    background-color: #3097D1;
		    color: white;
		}
		.total_area{
			text-align: center;
			margin: 20px 0;
		}
	</style>
</head>
<body>
	<div class="head_line">
		<h1>Yoga</h1>
		<h3>用戶 : {{ Auth::user()->name }}</h3>
		<h3>訂單編號 : {{ $order->order_no }}</h3>
	</div>

	<div style="margin-bottom: 20px;">
		<table>
			<tr>
				<th width="50%" style="text-align: left;background-color: white;color: black;">
					<h5>訂單建立時間 : {{ $order->created_at }}</h5>
				</th>
				<th width="50%" style="text-align: right;background-color: white;color: black;">
					<h5>訂單狀態 : {{ $status_array[$order->status] }}</h5>
				</th>
			</tr>
		</table>
	</div>

	<div class="content_area">
		<table>
			<tr>
				<th width="150">商品名</th>
				<th>圖片</th>
				<th width="50">數量</th>
				<th width="70">單價</th>
				<th width="70">小記</th>
			</tr>
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
		</table>

		{{-- <pagebreak> --}}

		<h3 class="total_area">
			<p>總價 : <strong style="color: red;">{{ $order->price }}</strong></p>
		</h3>
	</div>
</body>
</html>