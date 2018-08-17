@extends('layouts.app')

@if($category->parent_id==0)
    @section('title', 'Main Category : '.$category->name)
@else
    @section('title', 'Sub Category : '.$category->name)
@endif

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
    @if($errors->any())
        @foreach($errors->all() as $errors)
            <div class="alert alert-danger">
                <strong>{{ $errors }}</strong> 
            </div>
        @endforeach
    @endif

    <form id="article_form" action="{{ route('category.update', $category->id) }}" method="POST" data-parsley-validate>
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <div class="form-group">
            <label for="name">類別名稱</label>
            <input value="{{ old( 'name', $category->name) }}" type="text" class="form-control" id="name" name="name" maxlength="255" required>
        </div>

        @if( $category->parent_id==0 && sizeof($category->children)!=0 )
            <div class="form-group">
                <label>類別隸屬</label>
                <input value="主類別(底下仍有子類別)" type="text" class="form-control" disabled>
            </div>
        @else
            <div class="form-group">
                <label for="parent_id">類別隸屬</label>
                <select id="parent_id" class="form-control" name="parent_id">
                    <option value="0">主類別</option>
                    @foreach($cate_parent_list as $parent)
                        <option value="{{ $parent->id }}" {{ ($parent->id==$category->parent_id) ? 'selected' : '' }}>{{ $parent->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <button type="submit" class="btn btn-success btn-default">Submit</button>
        <a href="{{ route('category.index') }}" class="btn btn-success btn-default">Back</a>
    </form>
@endsection