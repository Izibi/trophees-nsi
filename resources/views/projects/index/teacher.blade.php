@extends('layout')

@section('content')
    @include('projects.contest-status.teacher')

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Projets</h2>
            @include('projects.index.filter')
        </div>
        @if(count($rows))
            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('id', 'ID') !!}</th>
                            <th>{!! SortableTable::th('name', 'Nom') !!}</th>
                            <th>{!! SortableTable::th('school_name', 'Établissement') !!}</th>
                            <th>{!! SortableTable::th('created_at', 'Date de soumission') !!}</th>
                            <th>{!! SortableTable::th('status', 'Statut') !!}</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $project)
                        <tr data-row-id="{{ $project->id }}"
                            @if($project->status == 'incomplete') class="row-alert" @endif
                            @if($project->status != 'draft') data-actions-disabled="edit" @endif
                            data-redirect-url="{{ $project->view_url }}">
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->school_name }}</td>
                            <td>{{ $project->created_at }}</td>
                            <td>@lang('project_status.'.$project->status)</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            @include('common.empty_list')
        @endif
    </div>

    @include('common.paginator')


    <div class="mt-5 mb-3">
        @if(count($rows))
            @if($contest->status == 'open')
                <button class="btn btn-primary active-button" data-action="/projects/:id/edit" data-method="GET" data-action-name="edit">Modifier le projet sélectionné</button>
            @endif
            <button class="btn btn-primary active-button" data-action="/projects/:id" data-method="REDIRECT">Afficher le projet sélectionné</button>
        @endif
        @if($contest->status == 'open')
            <button class="btn btn-primary active-button" data-action="/projects/create" data-method="GET">Déposer un nouveau projet</button>
        @endif
    </div>
@endsection