@extends('layouts.app')

@section('title', 'Article Panel')

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
				<span>文章列表</span>
			</div>
			<div class="col-sm-1">
				<a href="{{ route('article.create') }}" class="btn btn-success" role="button" style="width: 70px;">Create</a>
			</div>
		</div>

		<table id="article_table" class="table table-hover">
			<thead>
				<tr>
					<td>排序</td>
					<td>文章標題</td>
					<td>創建時間</td>
					<td>狀態</td>
					<td colspan="2">檢視</td>
				</tr>
			</thead>
			<tbody>
				@if(sizeof($article_list)>0)
					@foreach($article_list as $key => $article)
					<tr>
						<td>{{ ($article_list->currentPage()-1)*$article_list->perPage()+$key+1 }}</td>
						<td>{{ $article->title }}</td>
						<td>{{ $article->created_at }}</td>
						<td>
							{{ ($article->status == 1) ? "開放" : "關閉" }}
						</td>
						<td>
							<a href="{{ route('article.edit', $article->id) }}" class="btn btn-success" role="button" style="width: 70px;">Edit</a>
						</td>
						@if(Auth::check())
						<td>
							<form action="{{ route('article.destroy', $article->id) }}" method="post" onsubmit="return confirm('確認刪除?');">
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
						<td colspan="6">暫無資料</td>
					</tr>
				@endif
			</tbody>
		</table>
		@if($article_list->lastPage()>1)
		<div class="panel-footer panel-primary">
			{{ $article_list->links() }}
		</div>
		@endif
	</div>
@endsection