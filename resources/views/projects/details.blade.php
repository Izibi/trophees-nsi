<div class="row">
    <div class="col-12 col-sm-3 mb-3">
        @if(!is_null($project->image_file))
            <div style="height: 200px; background: center / cover no-repeat url({{ Storage::disk('uploads')->url($project->image_file) }})"></div>
        @else
            <div style="height: 200px;" class="photo-placeholder">
                <i class="icon-picture"></i>
            </div>
        @endif
    </div>

    <div class="col-12 col-sm-9">
        @if($projects_paginator && $projects_paginator->hasPages())
            <div class="float-right pagination-compact">
                {{ $projects_paginator->links() }}
            </div>
        @endif

        <h1>{{ $project->name }}</h1>
        @if($project->school_id)
            Établissement : {{ $project->school->name }}<br>
        @endif
        @if($project->grade_id)
            Niveau : {{ $project->grade->name }}<br>
        @endif
        Composition de l'équipe :
        @if(!is_null($project->team_girls))
            {{ $project->team_girls }} filles;
        @endif
        @if(!is_null($project->team_boys))
            {{ $project->team_boys }} garçons;
        @endif
        @if(!is_null($project->team_not_provided))
            {{ $project->team_not_provided }} non renseigné
        @endif
        <br>
        @if(!is_null($project->video))
            Vidéo : <a href="{{ $project->video }}" target="_blank">{{ $project->video }}</a>
        @endif
    </div>
</div>

@if(!is_null($project->description))
    <div class="mt-3">{!! nl2br($project->description) !!}</div>
@endif

<div class="row mt-3">
    @if(!is_null($project->presentation_file))
        <div class="col-6">
            <a href="{{ Storage::disk('uploads')->url($project->presentation_file) }}" target="_blank">
                <i class="icon-file-pdf"></i>
                PDF de presentation
            </a>
        </div>
    @endif
    @if(!is_null($project->zip_file))
        <div class="col-6">
            <a href="{{ Storage::disk('uploads')->url($project->zip_file) }}" target="_blank">
                <i class="icon-file-archive"></i>
                fichier ZIP
            </a>
        </div>
    @endif
</div>

<div class="mt-3">
    Statut : soumis le {{ $project->created_at }}, @lang('project_status.'.$project->status)
</div>