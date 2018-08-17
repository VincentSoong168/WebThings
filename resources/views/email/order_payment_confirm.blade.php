<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style type="text/css">
    	.panel{
    		margin: 20px 0;
    		font-family: Microsoft JhengHei;
    	}
    	.alert{
    		overflow: auto;
    	}
    	td{
    		vertical-align: middle !important;
    	}
    	.well{
    		margin-bottom: 5px;
    	}
    	.red_text{
    		color: #f73b3b;
    		font-weight: bold;
    	}
    </style>
</head>
<body>
	<div class="container">
		<div class="col-md-12 text-center">
			<h1>{{ config('app.name') }}</h1>
			<h5>just some laravel</h5>
		</div>

		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3>書籍訂購 -- 付款完成通知信</h3>
				</div>

				<div class="panel-body">
					<h5>本信件由系統自動發出, 請勿直接回覆</h5>

					<table class="table">
						<tbody>
							<tr>
								<td class="info" width="65%">
									<h5 class="red_text">訂單屬性 : 書籍訂購</h5>
								</td>
								<td class="info" style="border-left: dashed #898585 1px;">
									<h5 class="red_text">訂單編號 : {{ $order->order_no }}</h5>
									<h5 class="red_text">訂單時間 : {{ $order->created_at }}</h5>
									<h5 class="red_text">訂單序號 : {{ $order->id }}</h5>
								</td>
							</tr>
						</tbody>
					</table>

					<div class="well well-sm">訂購商品一覽</div>

					<table class="table table-hover">
						<thead>
							<tr>
								<th width="50">#</th>
								<th width="100">項目</th>
								<th>內容</th>
								<th width="100">數量</th>
							</tr>
						</thead>
						<tbody>
							@foreach(json_decode($order->list) as $product_id => $product)
								<tr>
									<th scope="row">{{ $loop->iteration }}</td>
									<td>書籍</td>
									<td>{{ $product->collection->name }}</td>
									<td>{{ $product->quantity }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>{{-- end of panel body --}}

				<div class="panel-footer text-right">
					訂單金額 : <span  class="red_text">{{ $order->price }}</span>
				</div>

			</div>{{-- end of panel --}}
		</div>{{-- end of col-md-12 --}}

	</div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
