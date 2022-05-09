@extends('layout')

@section('content')
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
                    @include('projects.index.admin_list_details')
                @endif
            </div>
        @else
            @include('common.empty_list')
        @endif
    </div>

    @include('common.paginator')

    <div class="mt-5 mb-3">
        @if(count($rows))
            <button class="btn btn-primary active-button" data-action="/projects/:id/edit" data-method="GET" data-action-name="edit">Modifier le projet sélectionné</button>
            <button class="btn btn-primary active-button" data-action="/projects/:id" data-method="REDIRECT">Afficher le projet sélectionné</button>
        @endif
    </div>
@endsection