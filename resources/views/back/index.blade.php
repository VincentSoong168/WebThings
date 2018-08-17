@extends('layouts.app')

@section('header.right')
<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
        {{ Auth::guard('admins')->user()->name }} <span class="caret"></span>
    </a>

    <ul class="dropdown-menu" role="menu">
        <li>
            <a href="{{ route('admin.logout') }}"
                onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                Logout
            </a>

            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</li>
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
