@extends('layouts.app')

@section('meta')
    <meta property="fb:app_id"        content="456728881460671"/>
    <meta property="og:url"           content="{{ Request::url() }}" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="{{ $article->title }}" />
    <meta property="og:description"   content="{{ $article->description }}" />
    <meta property="og:image"         content="{{ asset('storage/'.$article->image) }}" />
@endsection

@section('title', $article->title)

@section('facebook_plugin')
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = 'https://connect.facebook.net/zh_TW/sdk.js#xfbml=1&version=v3.1&appId=456728881460671&autoLogAppEvents=1';
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
@endsection

@section('header.right')
@include('sub.web_header_right')
@endsection

@section('content')
    <div>
        <h1>{{ $article->title }}</h1>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading" style="padding: 0;">
            <img src="{{ asset('storage/'.$article->image) }}" style="width: 100%;">
        </div>
        <div class="panel-body">
            <h5>作者 : {{ $article->author['name'] }}</h5>

            <h5>文章類別 : {{ $article->category['name'] or '尚未指定文章類別' }}</h5>

            <h5>更新日期 : {{ date("Y年m月d日 H:i:s", strtotime($article->updated_at)) }}</h5>

            <h5>建立日期 : {{ date('Y年m月d日 H:i:s', strtotime($article->created_at)) }}</h5>

            <hr>
            {!! $article->content !!}
        </div>
        <div class="panel-footer">
            <p>Article Tags</p>
            @forelse($article->tag as $tag)
                <a href="{{ route('page.article.list', ['tag_id' => $tag['id']]) }}" class="btn btn-primary btn-sm">{{ $tag['name'] }}</a>
            @empty
                <div class="alert alert-warning" role="alert">
                    <strong>尚未給予任何標籤</strong>
                </div>
            @endforelse
        </div>
    </div>

    <div class="fb-share-button" data-href="{{ Request::url() }}" data-layout="button_count" data-size="large" data-mobile-iframe="true">
        <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode( Request::url() ) }}&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">分享</a>
    </div>

    <div class="fb-comments" data-href="{{ Request::url() }}" data-numposts="5" data-width="100%"></div>
@endsection