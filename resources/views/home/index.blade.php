@extends('layout')

@section('content')
    @if($contest)
        <h1>{{ $contest->name }}</h1>
        <p>{!! $contest->message !!}</p>
    @endif

    @if (!Auth::check())
        <div class="justify-content-center">
            <a class="btn btn-secondary" href="/login">Se connecter ou créer un compte</a>
        </div>
    @endif

    @if($error_message)
        <div class="alert alert-danger mt-3">{{ $error_message }}</div>
    @endif
@endsection