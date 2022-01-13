<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="theme-color" content="#ffffff">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script src="{{ mix('js/vendor.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>
        <script src="{{ mix('js/manifest.js') }}"></script>        
        @yield('head')
    </head>

    <body>
        @if (Auth::check())
            @include('nav')
        @endif
        <main class="container mt-3 mb-5">
            @if (\Session::has('message'))
                <div class="alert alert-success">{!! \Session::get('message') !!}</div>
            @endif            
            @yield('content')
        </main>        
    </body>
</html>