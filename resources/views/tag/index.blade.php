@extends('layouts.app')

@section('title', 'Tag Panel')

@section('css')
	<style type="text/css">
		.submit_button, .cancel_button{
			width: 48%;
			display: none;
		}

		.name_input{
			display: none;
		}
	</style>
@endsection

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
	@if(!$errors->isEmpty())
	<div class="alert alert-danger">
        <strong>{{ $errors->first() }}</strong> 
    </div>
	@endif

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
    
    <div class="col-md-8">
		<div class="panel panel-primary">
			<div class="panel-heading">文章標籤列表</div>

			<div class="panel-body">
				<table id="tag_table" class="table table-hover">
					<thead>
						<tr>
							<td>排序</td>
							<td>標籤名稱</td>
							<td>編輯</td>
							<td>刪除</td>
						</tr>
					</thead>
					<tbody>
						@if(sizeof($tag_list)>0)
							@foreach($tag_list as $key => $tag)
							<tr>
								<td>{{ ($tag_list->currentPage()-1)*$tag_list->perPage()+$key+1 }}</td>
								<td>
									<input class="form-control name_input" type="text" name="name" value="{{ $tag->name }}">
									<input class="action_target" type="hidden" value="{{ route('tag.update', $tag->id) }}">
									<p class="origin_name">{{ $tag->name }}</p>
								</td>
								<td class="text-center">
									<button class="btn btn-success submit_button">Update</button>
									<button class="btn btn-warning cancel_button">Cancel</button>
									<button class="btn btn-block btn-primary edit_button">Edit</button>
								</td>
								<td>
									<form action="{{ route('tag.destroy', $tag->id) }}" method="post" onsubmit="return confirm('確認刪除?');">
										{{ csrf_field() }}
										{{ method_field('DELETE') }}
										<button type="submit" class="btn btn-block btn-danger">Delete</button>
									</form>
								</td>
							</tr>
							@endforeach
						@else
							<tr>
								<td class="text-center" colspan="4">暫無資料</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>

			@if($tag_list->lastPage()>1)
			<div class="panel-footer panel-primary">
				{{ $tag_list->links() }}
			</div>
			@endif
		</div>{{-- end of panel --}}
	</div>

	<div class="col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				創建文章標籤
			</div>

			<div class="panel-body">
				<form id="tag_form" action="{{ route('tag.store') }}" method="post">
					{{ csrf_field() }}
					<div class="form-group">
			            <label for="name">標籤名稱</label>
			            <input value="{{ old('name') }}" type="text" class="form-control" id="name" name="name" maxlength="255" required>
			        </div>

					<button type="submit" class="btn btn-primary btn-block">新增</button>
				</form>
			</div>
		</div>{{-- end of panel --}}
	</div>

	<form id="edit_form" action="" method="post">
		{{ csrf_field() }}
		{{ method_field('PUT') }}
		<input id="edit_name" type="hidden" name="name">
	</form>
@endsection

@section('javascript')
	<script type="text/javascript">
		$(document).ready(function(){
			$(".edit_button, .cancel_button").click(function() {
				edit_toggle(this);
			});

			$(".submit_button").click(function() {
				var route = $(this).closest("tr").find(".action_target").val();
				var name = $(this).closest("tr").find(".name_input").val();
				edit_submit(route, name);
			});
		});

		function edit_toggle(target) {
			var origin_name = $(target).closest("tr").find(".origin_name").text();

			$(target).parent().find("button").toggle();
			$(target).closest("tr").find(".origin_name").toggle();
			$(target).closest("tr").find(".name_input").val(origin_name);
			$(target).closest("tr").find(".name_input").toggle();
		}

		function edit_submit(route, name) {
			$('#edit_form').attr('action', route);
			$('#edit_name').val(name);
			$("#edit_form").submit();
		}
	</script>
@endsection