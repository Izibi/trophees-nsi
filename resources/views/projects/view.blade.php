@extends('layout')

@section('content')
    <h1>{{ $project->name }}</h1>

    @if($project->school_id)
        <p>
            School: {{ $project->school->name }}
        </p>
    @endif

    @if($project->grade_id)
        <p>
            Grade: {{ $project->grade->name }}
        </p>
    @endif    

    @if(!is_null($project->description))
        <p>
            <pre>{{ $project->description }}</pre>
        </p>
    @endif

    @if(!is_null($project->video_url))
        <p>
            Video: <a href="{{ $project->video_url }}">{{ $project->video_url }}</a>
        </p>
    @endif


    <p>
        Status: submitted on {{ $project->created_at }} {{ $project->status }}
    </p>

@endsection