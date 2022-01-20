@extends('layout')

@section('content')
    @if($contest)
        <h1>{{ $contest->name }}</h1>
        <p>{!! $contest->message !!}</p>
    @endif

    @if (!Auth::check())
        <p>Les projets pour les Trophées NSI doivent être déposés par l'enseignant. Si vous êtes enseignant, vous pouvez vous inscrire ou vous connecter ci-dessous.</p>
        <p>Si vous êtes élève, contactez votre professeur de NSI pour l'informer que vous souhaitez que votre équipe participe aux Trophées NSI.</p>

        <div class="justify-content-center">
            <a class="btn btn-secondary" href="/login">Se connecter ou créer un compte</a>
        </div>
    @endif

    @if($error_message)
        <div class="alert alert-danger mt-3">{{ $error_message }}</div>
    @endif
@endsection