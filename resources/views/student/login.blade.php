@extends('layout')

@section('content')
    <h1>Connexion élève</h1>

    <p>Connectez-vous avec le code à 8 caractères fourni par votre enseignant pour accéder à votre projet.</p>

    @if($error_message)
        <div class="alert alert-danger mt-3">{{ $error_message }}</div>
    @endif

    <div class="row mt-4">
        <div class="col-md-5">
            {!! Form::open()->route('student.login.submit') !!}
                {!! Form::text('code', 'Code du projet')->attrs([
                    'autocomplete' => 'off',
                    'autocapitalize' => 'none',
                    'spellcheck' => 'false',
                    'maxlength' => 8
                ]) !!}
                <button class="btn btn-primary mt-3" type="submit">Se connecter</button>
            {!! Form::close() !!}
        </div>
    </div>

    <p class="mt-4">
        <a href="/">Retour à l'accueil</a>
    </p>
@endsection
