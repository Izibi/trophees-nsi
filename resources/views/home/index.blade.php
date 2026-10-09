@extends('layout')

@section('content')
    @if($contest)
        <h1>{{ $contest->name }}</h1>
        <p>{!! $contest->message !!}</p>
    @endif

    @if (!Auth::check() && !Session::has('student_project_id'))
        <div class="d-flex justify-content-center">
            <a class="btn btn-secondary mr-2" href="/login">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 6px;" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
                Connexion enseignant / jury
            </a>
            <a class="btn btn-primary" href="/connexion-eleve">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 6px;" aria-hidden="true"><path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1 2.7 3 6 3s6-2 6-3v-5"/></svg>
                Connexion élève
            </a>
        </div>
    @endif

    @if($error_message)
        <div class="alert alert-danger mt-3">{{ $error_message }}</div>
    @endif
@endsection