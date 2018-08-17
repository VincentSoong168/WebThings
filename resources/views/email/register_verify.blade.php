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
					<h3>申請會員 -- 信箱認證</h3>
				</div>

				<div class="panel-body">
					<h5>本信件由系統自動發出, 請勿直接回覆</h5>

					<h5>請點擊下方連結進行會員申請( 連結有效時間為10分鐘 )</h5>

					<a href="{{ route('register.form', [$token, $email]) }}">{{ route('register.form', [$token, $email]) }}</a>
				</div>{{-- end of panel body --}}

			</div>{{-- end of panel --}}
		</div>{{-- end of col-md-12 --}}

	</div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>