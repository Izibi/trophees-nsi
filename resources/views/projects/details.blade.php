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

<p>
    Project composition:
    @if(!is_null($project->team_girls))
        {{ $project->team_girls }} girls;
    @endif
    @if(!is_null($project->team_boys))
        {{ $project->team_boys }} boys;
    @endif            
    @if(!is_null($project->team_not_provided))
        {{ $project->team_not_provided }} not provided
    @endif                        
</p>

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