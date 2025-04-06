@extends('layout')

@section('content')
@if(!$view)
    La phase actuelle du concours ne vous permet pas d'évaluer des projets.
@else
    @include('projects.index.jury_awards_alert')

    <h3>
    @if($view['type'] == 'own')
        Liste de vos projets
    @elseif($view['type'] == 'region')
        Liste des projets de la région {{ $view['name'] }}
    @elseif($view['type'] == 'prize')
        Liste des projets nominés pour le prix {{ $view['name'] }}
    @endif
    </h3>

    <p>@include('projects.contest-status')</p>

    @if(count($other_views))
        @foreach($other_views as $other_view)
            @if($other_view['type'] == 'own')
                <a class="btn btn-primary" href="/projects">Voir mes projets</a>
            @elseif($other_view['type'] == 'region')
                <a class="btn btn-primary" href="/projects?type=region&id={{ $other_view['target_id'] }}">Voir les projets de la région {{ $other_view['name'] }}</a>
            @elseif($other_view['type'] == 'prize')
                <a class="btn btn-primary" href="/projects?type=prize&id={{ $other_view['target_id'] }}">Voir les projets nominés pour le prix {{ $other_view['name'] }}</a>
            @endif
        @endforeach
    @endif

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Projets</h2>
            @include('projects.index.common.rating_mode_switcher')
            @include('projects.index.common.filter')
        </div>
        @if(count($rows))
            <div class="table-responsive">
                @if($rating_mode)
                    @include('projects.index.common.list_ratings')
                @else
                    @include('projects.index.jury_list_details')
                @endif
            </div>
        @else
            @include('common.empty_list')
        @endif
    </div>

    @include('common.paginator')


    <div class="mt-5 mb-3">
        @if(count($rows))
            @if($view['edit'])
                <button class="btn btn-primary active-button" data-action="/projects/:id/edit" data-method="GET" data-action-name="edit">
                    Modifier le projet sélectionné
                </button>
            @endif
            @if($view['type'] != 'own' || !$view['edit'])
                <button class="btn btn-primary active-button" data-action="/projects/:id/view" data-method="GET">Afficher le projet sélectionné</button>
            @endif
            @if($view['type'] != 'own' && $coordinator)
                <a class="btn btn-primary active-button" data-action="" target="_blank" href="/projects_export">Télécharger au format CSV</a>
		    @endif
        @endif
        @if($view['create'])
            <button class="btn btn-primary active-button" data-action="/projects/create" data-method="GET">Déposer un nouveau projet</button>
        @endif
    </div>
    @if(!$view['create'])
    <hr>
    <div class="mt-3 mb-3">
        Outils d'évaluation :<br>
        <a class="btn btn-primary" target="_blank" href="/projects/zips/{{ $zip_name }}">Télécharger tous ces projets dans une archive ZIP</a>
        <a class="btn btn-primary" target="_blank" href="/evaluation_server/">Evaluer ces projets en ligne</a>
    </div>
    @endif
@endif
@endsection
