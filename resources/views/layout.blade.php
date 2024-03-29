<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <meta name="theme-color" content="#ffffff">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script src="{{ mix('js/vendor.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>
        <script src="{{ mix('js/manifest.js') }}"></script>
        <title>Trophés NSI - dépôt de projets</title>
        @yield('head')
    </head>

    <body>
        @if (Auth::check())
            @include('nav')
        @endif
	<main class="container mt-5 mb-5" style="{{ $containerstyle ?? '' }}">
            @if (\Session::has('message'))
                <div class="alert alert-success">{!! \Session::get('message') !!}</div>
            @endif
            @if (\Session::has('error'))
                <div class="alert alert-danger">{!! is_array(\Session::get('error')) ? implode('<br>', \Session::get('error')) : \Session::get('error') !!}</div>
            @endif
            @yield('content')
        </main>
    </body>
</html>
