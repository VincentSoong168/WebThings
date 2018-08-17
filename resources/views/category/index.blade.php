@extends('layouts.app')

@section('title', 'Category Panel')

@section('css')
	<style type="text/css">
		.children_tr{
			display: none;
			background-color: #f5f5f5;
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
    
    <div class="col-md-8">
		<div class="panel panel-primary">
			<div class="panel-heading">
				文章類別列表
			</div>

			<div class="panel-body">
				<table id="category_table" class="table table-hover">
					<thead>
						<tr>
							<td>排序</td>
							<td>類別名稱</td>
							<td>隸屬類別</td>
							<td>編輯</td>
							<td>刪除</td>
						</tr>
					</thead>
					<tbody>
						@if(sizeof($category_list)>0)
							@foreach($category_list as $key => $category)
							<tr class="parent_tr" category_id="{{ $category->id }}">
								<td>{{ ($category_list->currentPage()-1)*$category_list->perPage()+$key+1 }}</td>

								@if(sizeof($category->children)>0)
								<td><a href="#" class="toggle_trigger">{{ $category->name }}</a></td>
								@else
								<td>{{ $category->name }}</td>
								@endif

								<td>{{ $category->parent['name'] or '主類別' }}</td>
								<td>
									<a href="{{ route('category.edit', $category->id) }}" class="btn btn-block btn-primary">Edit</a>
								</td>
								<td>
									<form action="{{ route('category.destroy', $category->id) }}" method="post" onsubmit="return confirm('確認刪除?');">
										{{ csrf_field() }}
										{{ method_field('DELETE') }}
										<button type="submit" class="btn btn-block btn-danger">Delete</button>
									</form>
								</td>
							</tr>

							@if(sizeof($category->children)>0)
							@foreach($category->children as $children_key => $children)
							<tr class="children_tr" parent_id="{{ $category->id }}">
								<td>{{ ($category_list->currentPage()-1)*$category_list->perPage()+$key+1 }}-{{ $children_key+1 }}</td>
								<td>|--{{ $children->name }}</td>
								<td>{{ $children->parent['name'] or '主類別' }}</td>
								<td>
									<a href="{{ route('category.edit', $children->id) }}" class="btn btn-block btn-primary">Edit</a>
								</td>
								<td>
									<form action="{{ route('category.destroy', $children->id) }}" method="post" onsubmit="return confirm('確認刪除?');">
										{{ csrf_field() }}
										{{ method_field('DELETE') }}
										<button type="submit" class="btn btn-block btn-danger">Delete</button>
									</form>
								</td>
							</tr>
							@endforeach
							@endif
								
							@endforeach
						@else
							<tr>
								<td colspan="5">暫無資料</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>

			@if($category_list->lastPage()>1)
			<div class="panel-footer panel-primary">
				{{ $category_list->links() }}
			</div>
			@endif
		</div>{{-- end of panel --}}
	</div>

	<div class="col-md-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				創建文章類別
			</div>

			<div class="panel-body">
				@if(!$errors->isEmpty())
				<div class="alert alert-danger">
			        <strong>{{ $errors->first() }}</strong> 
			    </div>
				@endif
				<form id="category_form" action="{{ route('category.store') }}" method="post">
					{{ csrf_field() }}
					<div class="form-group">
			            <label for="name">類別名稱</label>
			            <input value="{{ old('name') }}" type="text" class="form-control" id="name" name="name" maxlength="255" required>
			        </div>
					
					<div class="form-group">
						<label for="parent_id">類別隸屬</label>
						<select id="parent_id" class="form-control" name="parent_id">
							<option value="0">主類別</option>
							@foreach($cate_parent_list as $parent)
							<option value="{{ $parent->id }}">{{ $parent->name }}</option>
							@endforeach
						</select>
					</div>

					<button type="submit" class="btn btn-primary btn-block">新增</button>
				</form>
			</div>
		</div>{{-- end of panel --}}
	</div>
@endsection

@section('javascript')
	<script type="text/javascript">
		$(document).ready(function(){
			$(".toggle_trigger").on("click", function(){
				var parent_id = $(this).closest("tr").attr("category_id");
				$(".children_tr[parent_id='"+parent_id+"']").toggle();
			});
		});
	</script>
@endsection