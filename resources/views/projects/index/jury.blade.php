@extends('layout')

@section('content')
    @include('projects.contest-status.jury')
    @include('projects.index.jury_awards_alert')

    <p>
    @if($prize === null)
        Liste des projets soumis pour : <b>{{ $user->region->name }}</b>
    @else
        Liste des projets nominés pour le prix national <b>"{{ $prize->name }}"</b>
    @endif
    </p>

    @if(count($user->prizes))
<!--        <p>
        @if($prize !== null)
            <a class="btn btn-primary" href="/projects">Afficher les projets de la région</a>
        @endif
        @foreach ($user->prizes as $p)
            @if($prize !== $p)
                <a class="btn btn-primary" href="/projects?prize_id={{ $p->id }}">Afficher les projets nominés pour "{{ $p->name }}"</a>
            @endif
        @endforeach
        </p>-->
    @endif

    @if($prize === null)
    Le jury national est en cours de délibération.
    @else
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
            @if($contest->status == 'grading' || $contest->status == 'deliberating' || $contest->status == 'closed')
                <button class="btn btn-primary active-button" data-action="/projects/:id" data-method="REDIRECT">Afficher le projet sélectionné</button>
            	@if($user_is_coordinator)
            		<a class="btn btn-primary active-button" data-action="" target="_blank" href="/projects_export">Télécharger les scores au format CSV</a>
		@endif
            @endif
        @endif
    </div>
    @endif
@endsection
