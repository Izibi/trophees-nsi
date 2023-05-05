@extends('layout')

@section('content')
    @include('projects.contest-status.jury')
    @include('projects.index.jury_awards_alert')

    Liste des projets soumis pour : <b>{{ $user->region->name }}</b>

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
            @endif
        @endif
    </div>
@endsection
