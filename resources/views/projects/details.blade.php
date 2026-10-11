<div class="row">
    <div class="col-12 col-sm-3 mb-3">
        @if(!is_null($project->image_file))
            <div style="height: 200px; background: center / contain no-repeat url({{ Storage::disk('uploads')->url($project->image_file) }})"></div>
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
        @if($project->school_id && $project->school->academy_id)
            Académie : {{ $project->school->academy->name }}<br>
        @endif
        @if($project->school_id)
            Établissement : {{ $project->school ? $project->school->name : '' }}<br>
        @endif
        @if($project->grade_id)
            Niveau : {{ $project->grade->name }}<br>
        @endif

        @if(Auth::user()->role == 'jury' && count($project->team_members))
            Équipe :
            @foreach($project->team_members as $member)
                {{ $member->first_name }} {{ $member->last_name }}@if(!$loop->last), @endif
            @endforeach
            <br>
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

        Totaux en NSI pour ce niveau :
        @if(!is_null($project->class_girls))
            {{ $project->class_girls }} filles;
        @endif
        @if(!is_null($project->class_boys))
            {{ $project->class_boys }} garçons;
        @endif
        @if(!is_null($project->class_not_provided))
            {{ $project->class_not_provided }} non renseigné
        @endif
        <br>
    </div>
</div>

<div class="row mt-3">
    @if(!is_null($project->parental_permissions_file) && Auth::user()->role != 'jury')
        <div class="col-6">
            <a href="{{ Storage::disk('uploads')->url($project->parental_permissions_file) }}" target="_blank">
                <i class="icon-file-pdf"></i>
                Autorisations parentales
            </a>
        </div>
    @endif

    @if(Auth::user()->role != 'jury' && count($project->team_members))
        <div class="col-12">
            <table class="table">
                @foreach($project->team_members as $team_member)
                    <tr>
                        <td>
                            {{ $team_member->first_name }}
                            {{ $team_member->last_name }}
                        </td>
                        <td class="text-right">
                            <a href="{{ Storage::disk('uploads')->url($team_member->parental_permissions_file) }}" target="_blank">
                                <i class="icon-file-pdf"></i>
                                Autorisations parentales
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif
</div>

<div class="card mb-4">
    <div class="card-body">
        @if(!is_null($project->work_organisation))
            <div class="mb-3">
                <b>Organisation du travail et répartition des tâches :</b><br>
                {!! nl2br($project->work_organisation) !!}
            </div>
        @endif

        @if(!is_null($project->teacher_notes))
            <div>
                <b>Informations complémentaires (commentaire de l'enseignant sur le projet et l'équipe) :</b><br>
                {!! nl2br($project->teacher_notes) !!}
            </div>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h4>Présentation du projet</h4>
    </div>
    <div class="card-body">
        @if(!is_null($project->description))
            <div class="mb-3">
                <b>Résumé du projet :</b><br>
                {!! nl2br($project->description) !!}
            </div>
        @endif
        @if(!is_null($project->project_description))
            <div class="mb-3">
                <b>Description du projet :</b><br>
                {!! nl2br($project->project_description) !!}
            </div>
        @endif
        @if(!is_null($project->project_steps))
            <div class="mb-3">
                <b>Étapes de réalisation du projet :</b><br>
                {!! nl2br($project->project_steps) !!}
            </div>
        @endif
        @if(!is_null($project->project_outlook))
            <div class="mb-3">
                <b>Ouverture :</b><br>
                {!! nl2br($project->project_outlook) !!}
            </div>
        @endif
        @if(!is_null($project->video))
            <div class="mb-3">
                <b>Démonstration vidéo :</b>
                <a href="{{ $project->video }}" target="_blank">{{ $project->video }}</a>
            </div>
        @endif
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h4>Informations techniques</h4>
    </div>
    <div class="card-body">
        @if(!is_null($project->url))
            <div class="mb-3">
                <b>Dossier technique :</b>
                <a href="{{ $project->url }}" target="_blank">{{ $project->url }}</a>
            </div>
        @endif
        @if(!is_null($project->code_notes))
            <div class="mb-3">
                <b>Nature du code :</b><br>
                {!! nl2br($project->code_notes) !!}
            </div>
        @endif
        @if(!is_null($project->operationality_notes))
            <div class="mb-3">
                <b>Validation de l'opérationnalité du projet / de son fonctionnement :</b><br>
                {!! nl2br($project->operationality_notes) !!}
            </div>
        @endif
        @if(!is_null($project->ai_usage_percent))
            <div class="mb-3">
                <b>Part d'utilisation de l'IA :</b>
                {{ $project->ai_usage_percent }}{{ is_numeric($project->ai_usage_percent) ? ' %' : '' }}
            </div>
        @endif
        @if(!is_null($project->ai_usage_notes))
            <div class="mb-3">
                <b>Usage de l'IA :</b><br>
                {!! nl2br($project->ai_usage_notes) !!}
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    Statut : soumis le {{ $project->created_at }}, @lang('project_status.'.$project->status)
</div>

<hr>

@foreach($awards as $award)
    <div class="mt-3">
        <b>Nommé pour le {{ $award->getPrizeTitle() }} par {{ $award->user->name }}</b><br>
        @if($award->comment && (Auth::user()->role == 'jury' || (isset($can_award) && $can_award)))
            <div class="mt-2">
                <i>Commentaire pour le jury :</i><br>
                {{ $award->comment }}
            </div>
        @endif
        @if($award->comment_team && (Auth::user()->role == 'teacher' || (isset($can_award) && $can_award)))
            <div class="mt-2">
                <i>Commentaire pour l'équipe lauréate :</i><br>
                {{ $award->comment_team }}
            </div>
        @endif
    </div>
@endforeach