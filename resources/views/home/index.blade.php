@extends('layout')

@section('content')
    @if (!Auth::check())
        <p>Projects have to be deposited by teachers. If you are a teacher, you can register or login below.</p>
        <p>If you are a student, please contact your NSI teacher and let them know you would like your group to take part in Trophees NSI.</p>

        <div class="justify-content-center">
            <a class="btn btn-primary" href="/login">Login or Register</a>
        </div>
    @endif

    @if($error_message)
        <div class="alert alert-danger mt-3">{{ $error_message }}</div>
    @endif
@endsection