@extends('layout')

@section('content')
    <div class="row">
        <div class="col-6">
            @include('projects.details')
        </div>
        <div class="col-6">
            @include('projects.rating.edit')
        </div>
    </div>
@endsection