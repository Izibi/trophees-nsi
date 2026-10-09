@extends('layout')

@section('content')
    <h1>Connexion élève</h1>

    <div class="alert alert-warning mt-3">{{ $message }}</div>

    <a class="btn btn-primary" href="/deconnexion-eleve">Se déconnecter</a>
@endsection
