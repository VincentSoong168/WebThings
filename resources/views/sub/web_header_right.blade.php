@if (Auth::check())
<li class="dropdown">
    <a class="remove_padding" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
        @if(Auth::guard('web')->user()->image)
            <img class="header_right_img" src="{{ asset('storage/'.Auth::guard('web')->user()->image) }}" width="50" height="50">
        @else
            <img class="header_right_img" src="{{ asset('storage/user/default.jpeg') }}" width="50" height="50">
        @endif
        {{ Auth::guard('web')->user()->name }} <span class="caret"></span>
    </a>

    <ul class="dropdown-menu" role="menu">
        <li><a href="{{ route('user.edit', Auth::id()) }}">User Panel</a></li>
        <li><a href="{{ route('cart.list') }}">Order List</a></li>
        <hr>
        <li><a href="{{ route('article.index') }}">Article Panel</a></li>
        <li><a href="{{ route('category.index') }}">Category Panel</a></li>
        <li><a href="{{ route('tag.index') }}">Tag Panel</a></li>
        <hr>
        <li><a href="{{ route('product.index') }}">Product Panel</a></li>
        <hr>
        <li>
            <a href="{{ route('user.logout') }}"
                onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                Logout
            </a>

            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</li>
@endif