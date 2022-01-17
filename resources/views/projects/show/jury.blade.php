@extends('layout')

@section('content')
    <div class="row">
        <div class="col-8">
            @include('projects.details')
        </div>
        <div class="col-4">
            @include('projects.rating.edit')
        </div>
    </div>
@endsection