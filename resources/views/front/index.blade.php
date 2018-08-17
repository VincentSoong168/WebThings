@extends('layouts.app')

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (Auth::guard('web')->check())
                        <div class="text-success">User({{Auth::guard('web')->user()->name}}) is logged in!</div>
                    @else
                        <div class="text-danger">User is not logged in!</div>
                    @endif

                    <br>

                    @if (Auth::guard('admins')->check())
                        <div class="text-success">Admin({{Auth::guard('admins')->user()->name}}) is logged in!</div>
                    @else
                        <div class="text-danger">Admin is not logged in!</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
